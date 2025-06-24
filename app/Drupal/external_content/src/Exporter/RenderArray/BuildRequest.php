<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\ContentNode;

final readonly class BuildRequest {

  public function __construct(
    public ContentNode $currentAstNode,
    public ArrayElement $currentArrayElement,
    public ExportRequest $exportRequest,
  ) {}

  public function withNewAstNode(ContentNode $new_content_node): self {
    return new self($new_content_node, $this->currentArrayElement, $this->exportRequest);
  }

  public function withNewArrayElement(ArrayElement $new_array_element): self {
    return new self($this->currentAstNode, $new_array_element, $this->exportRequest);
  }

}
