<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\MediaReference;

use Drupal\external_content\Contract\Builder\Array\Builder;
use Drupal\external_content\Contract\Builder\Array\ChildBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\Array\Builder<\Drupal\niklan\ExternalContent\Nodes\MediaReference\MediaReference>
 */
final readonly class ArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof MediaReference;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): ArrayElement {
    $element = new ArrayElement($node::getNodeType(), [
      'uuid' => $node->uuid,
      'metadata' => $node->metadata,
    ]);
    $child_builder->buildChildren($node, $element);
    return $element;
  }

}
