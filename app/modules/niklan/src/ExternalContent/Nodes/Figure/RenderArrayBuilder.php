<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Figure;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\HtmlTagHelper;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\niklan\ExternalContent\Nodes\Figure\Figure>
 */
final readonly class RenderArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof Figure;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => 'figure',
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        HtmlTagHelper::preRenderTag(...),
      ],
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
