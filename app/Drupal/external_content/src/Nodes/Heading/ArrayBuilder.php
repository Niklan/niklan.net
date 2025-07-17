<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Builder\Array\Builder;
use Drupal\external_content\Contract\Builder\Array\ChildBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\Array\Builder<\Drupal\external_content\Nodes\Heading\Heading>
 */
final readonly class ArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof Heading;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): ArrayElement {
    $element = new ArrayElement($node::getNodeType(), ['tag' => $node->tag->value]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
