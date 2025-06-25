<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Content\Content;

final readonly class ArrayBuildRequest {

  public function __construct(
    public Content $currentAstNode,
    public ArrayElement $currentArrayElement,
    public ArrayExportRequest $exportRequest,
  ) {}

  public function withNewAstNode(Content $new_content_node): self {
    return new self($new_content_node, $this->currentArrayElement, $this->exportRequest);
  }

  public function withNewArrayElement(ArrayElement $new_array_element): self {
    return new self($this->currentAstNode, $new_array_element, $this->exportRequest);
  }

}
