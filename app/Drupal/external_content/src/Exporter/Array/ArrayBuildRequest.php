<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

final readonly class ArrayBuildRequest {

  public function __construct(
    public Node $node,
    public ArrayElement $arrayElement,
    public ArrayExportRequest $request,
  ) {}

  public function withNewNode(Node $new_content_node): self {
    return new self($new_content_node, $this->arrayElement, $this->request);
  }

  public function withNewArrayElement(ArrayElement $new_array_element): self {
    return new self($this->node, $new_array_element, $this->request);
  }

}
