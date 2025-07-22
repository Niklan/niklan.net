<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\HtmlTagHelper;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\external_content\Nodes\Heading\Heading>
 * @deprecated Exactly the same as HtmlElement.
 */
final readonly class RenderArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof Heading;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => $node->tag->value,
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        HtmlTagHelper::preRenderTag(...),
      ],
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
