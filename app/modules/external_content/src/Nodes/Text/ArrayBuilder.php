<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Contract\Builder\Array\Builder;
use Drupal\external_content\Contract\Builder\Array\ChildBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\Array\Builder<\Drupal\external_content\Nodes\Text\Text>
 */
final readonly class ArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof Text;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): ArrayElement {
    \assert($node instanceof Text);
    $element = new ArrayElement($node::getNodeType(), ['text' => $node->text]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
