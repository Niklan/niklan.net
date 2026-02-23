<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Builder\Array\Builder;
use Drupal\external_content\Contract\Builder\Array\ChildBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\Array\Builder<\Drupal\app_blog\ExternalContent\Nodes\CodeBlock\CodeBlock>
 */
final readonly class ArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof CodeBlock;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): ArrayElement {
    $element = new ArrayElement($node::getNodeType(), [
      'code' => $node->code,
      'attributes' => $node->attributes,
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
