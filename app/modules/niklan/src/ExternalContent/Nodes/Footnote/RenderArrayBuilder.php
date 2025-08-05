<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Footnote;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\HtmlElement\HtmlElement;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\HtmlTagHelper;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\external_content\Nodes\HtmlElement\HtmlElement>
 */
final readonly class RenderArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof HtmlElement
      && isset($node->attributes['class'])
      && \str_contains($node->attributes['class'], 'footnote-ref');
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => $node->tag,
      '#attributes' => $node->attributes,
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        HtmlTagHelper::preRenderTag(...),
      ],
      '#attached' => [
        'library' => [
          'niklan/footnote.tooltip',
        ],
      ],
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
