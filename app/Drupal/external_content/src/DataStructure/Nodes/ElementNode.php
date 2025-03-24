<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

use Drupal\external_content\DataStructure\NodeProperties;

/**
 * Provides a node for nodes with additional properties.
 */
abstract class ElementNode extends ContentNode {

  public function __construct(
    string $type,
    protected NodeProperties $properties = new NodeProperties(),
  ) {
    parent::__construct($type);
  }

  public function getProperties(): NodeProperties {
    return $this->properties;
  }

}
