<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

final readonly class ArrayParseRequest {

  public function __construct(
    public ArrayElement $currentArrayElement,
    public Node $currentAstNode,
    public ArrayImportRequest $importRequest,
  ) {}

  public function withNewContentNode(Node $new_content_node): self {
    return new self($this->currentArrayElement, $new_content_node, $this->importRequest);
  }

  public function withNewArrayElement(ArrayElement $new_array_element): self {
    return new self($new_array_element, $this->currentAstNode, $this->importRequest);
  }

}
