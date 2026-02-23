<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\ArticleLink;

use Drupal\Core\Database\Connection;
use Drupal\Core\Render\Element\HtmlTag;
use Drupal\Core\Url;
use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\HtmlElement\HtmlElement;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\HtmlTagHelper;
use Drupal\app_blog\ExternalContent\Stages\ArticleTranslationFieldUpdater;
use Drupal\app_blog\ExternalContent\Stages\LinkProcessor;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\external_content\Nodes\HtmlElement\HtmlElement>
 * @see \Drupal\app_blog\ExternalContent\Stages\LinkProcessor::markAsInternalArticleLink
 * @see \Drupal\app_blog\ExternalContent\Stages\ArticleTranslationFieldUpdater::syncExternalContent
 */
final readonly class RenderArrayBuilder implements Builder {

  public function __construct(
    private Connection $database,
  ) {}

  public function supports(Node $node): bool {
    return $node instanceof HtmlElement
      && $node->tag === 'a'
      && isset($node->attributes[LinkProcessor::DATA_HASH_ATTRIBUTE]);
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $hash = $node->attributes[LinkProcessor::DATA_HASH_ATTRIBUTE];
    $node->attributes['href'] = $this->findDestination($hash);
    unset($node->attributes[LinkProcessor::DATA_HASH_ATTRIBUTE]);

    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#attributes' => $node->attributes,
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        HtmlTagHelper::preRenderTag(...),
      ],
      '#cache' => [
        'tags' => [
          'external_content:' . ArticleTranslationFieldUpdater::SOURCE_PATH_HASH_PROPERTY,
        ],
      ],
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

  private function findDestination(string $link_checksum): ?string {
    $query = $this
      ->database
      ->select('node__external_content', 'ecf')
      ->condition('ecf.bundle', 'blog_entry')
      ->fields('ecf', ['entity_id'])
      ->where(
        snippet: "JSON_CONTAINS(ecf.external_content_data, :search_value, :json_path)",
        args: [
          ':json_path' => '$.' . ArticleTranslationFieldUpdater::SOURCE_PATH_HASH_PROPERTY,
          ':search_value' => '"' . $link_checksum . '"',
        ])
      ->range(0, 1);

    $article_id = $query->execute()?->fetchField();

    return $article_id ? Url::fromRoute(
      route_name: 'entity.node.canonical',
      route_parameters: [
        'node' => $article_id,
      ],
    )->toString() : NULL;
  }

}
