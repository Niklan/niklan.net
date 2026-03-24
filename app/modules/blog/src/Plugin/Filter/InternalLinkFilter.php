<?php

declare(strict_types=1);

namespace Drupal\app_blog\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Filter(
  id: self::ID,
  title: new TranslatableMarkup('Blog internal links'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  description: new TranslatableMarkup('Resolves data-source-path-hash attributes to actual URLs.'),
  weight: 3,
)]
final class InternalLinkFilter extends FilterBase implements ContainerFactoryPluginInterface {

  public const string ID = 'app_blog_internal_link';
  public const string DATA_HASH_ATTRIBUTE = 'data-source-path-hash';

  /**
   * Node IDs indexed by hash, populated during resolveHashes().
   *
   * @var array<string, int>
   */
  private array $nodeIdsByHash = [];

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get(Connection::class),
    );
  }

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly Connection $database,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    if (!\str_contains($text, self::DATA_HASH_ATTRIBUTE)) {
      return new FilterProcessResult($text);
    }

    $dom = Html::load($text);
    $links = $this->queryLinks($dom);

    if (!$links) {
      return new FilterProcessResult($text);
    }

    $url_map = $this->resolveHashes($this->collectHashes($links));
    $cache_tags = [];

    foreach ($links as $link) {
      $cache_tags = [...$cache_tags, ...$this->applyUrl($link, $url_map)];
    }

    $result = new FilterProcessResult(Html::serialize($dom));
    $result->addCacheTags($cache_tags);

    return $result;
  }

  /**
   * @return list<\DOMElement>
   */
  private function queryLinks(\DOMDocument $dom): array {
    $xpath = new \DOMXPath($dom);
    $node_list = $xpath->query('//a[@' . self::DATA_HASH_ATTRIBUTE . ']');

    if (!$node_list || $node_list->length === 0) {
      return [];
    }

    $links = [];
    foreach ($node_list as $node) {
      \assert($node instanceof \DOMElement);
      $links[] = $node;
    }

    return $links;
  }

  /**
   * @param list<\DOMElement> $links
   *
   * @return array<string, string>
   */
  private function collectHashes(array $links): array {
    $hashes = [];
    foreach ($links as $link) {
      $hash = $link->getAttribute(self::DATA_HASH_ATTRIBUTE);
      $hashes[$hash] = $hash;
    }
    return $hashes;
  }

  /**
   * @param array<string, string> $url_map
   *
   * @return list<string>
   */
  private function applyUrl(\DOMElement $link, array $url_map): array {
    $hash = $link->getAttribute(self::DATA_HASH_ATTRIBUTE);
    $link->removeAttribute(self::DATA_HASH_ATTRIBUTE);

    if (!isset($url_map[$hash])) {
      return [];
    }

    $link->setAttribute('href', $url_map[$hash]);

    return ['node:' . $this->nodeIdsByHash[$hash]];
  }

  /**
   * @param array<string, string> $hashes
   *
   * @return array<string, string>
   */
  private function resolveHashes(array $hashes): array {
    if (!$hashes) {
      return [];
    }

    $results = $this->database
      ->select('node__field_source_path_hash', 'h')
      ->condition('h.bundle', 'blog_entry')
      ->condition('h.field_source_path_hash_value', \array_values($hashes), 'IN')
      ->fields('h', ['entity_id', 'field_source_path_hash_value'])
      ->execute();

    $url_map = [];
    foreach ($results ?? [] as $row) {
      $nid = (int) $row->entity_id;
      $this->nodeIdsByHash[$row->field_source_path_hash_value] = $nid;
      $url_map[$row->field_source_path_hash_value] = Url::fromRoute('entity.node.canonical', ['node' => $nid])->toString();
    }

    return $url_map;
  }

}
