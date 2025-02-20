<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\RenderArrayBuilder;

use Drupal\Core\Database\Connection;
use Drupal\Core\Render\Element\HtmlTag;
use Drupal\Core\Url;
use Drupal\external_content\Builder\ElementRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Utils\RenderArrayBuilderHelper;

/**
 * @see \Drupal\niklan\ExternalContent\Loader\Blog::prepareInternalLinks
 *
 * @ingroup content_sync
 */
final readonly class Link implements RenderArrayBuilderInterface {

  public function __construct(
    private Connection $connection,
  ) {}

  #[\Override]
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof Element);
    $attributes = $node->getAttributes();
    $link_checksum = $attributes->getAttribute('data-pathname-md5');
    \assert(\is_string($link_checksum));
    $destination = $this->findDestination($link_checksum);

    return RenderArrayBuilderResult::withRenderArray([
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#attributes' => [
        'href' => $destination ? $destination->toString() : '#',
      ],
      '#pre_render' => [
        [HtmlTag::class, 'preRenderHtmlTag'],
        [ElementRenderArrayBuilder::class, 'preRenderTag'],
      ],
      '#cache' => [
        'tags' => [
          'external_content:' . $link_checksum,
        ],
      ],
      'children' => RenderArrayBuilderHelper::buildChildren(
        node: $node,
        child_builder: $child_builder,
      )->result(),
    ]);
  }

  #[\Override]
  public function supportsBuild(NodeInterface $node): bool {
    if (!$node instanceof Element) {
      return FALSE;
    }

    $attributes = $node->getAttributes();

    if (!$attributes->hasAttribute('data-selector')) {
      return FALSE;
    }

    $is_link = $node->getTag() === 'a';
    $is_external_link = $attributes->getAttribute('data-selector') === 'niklan:external-link';
    $is_has_md5 = $attributes->hasAttribute('data-pathname-md5');

    return $is_link && $is_external_link && $is_has_md5;
  }

  private function findDestination(string $link_checksum): ?Url {
    $query = $this
      ->connection
      ->select('node__external_content', 'ecf')
      ->condition('ecf.bundle', 'blog_entry')
      ->fields('ecf', ['entity_id'])
      ->where(
        snippet: "JSON_CONTAINS(ecf.external_content_data, :search_value, :json_path)",
        args: [
          ':json_path' => '$.pathname_md5',
          ':search_value' => '"' . $link_checksum . '"',
        ])
      ->range(0, 1);

    $blog_id = $query->execute()?->fetchField();

    return $blog_id ? Url::fromRoute(
      route_name: 'entity.node.canonical',
      route_parameters: [
        'node' => $blog_id,
      ],
    ) : NULL;
  }

}
