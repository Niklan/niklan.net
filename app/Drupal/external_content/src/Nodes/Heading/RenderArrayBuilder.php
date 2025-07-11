<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\external_content\Nodes\Heading\Heading>
 */
final readonly class RenderArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof Heading;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => $node->tagType->value,
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
