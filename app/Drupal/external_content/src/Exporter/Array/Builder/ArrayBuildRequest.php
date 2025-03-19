<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array\Builder;

use Drupal\external_content\Exporter\Array\ArrayExportRequest;
use Drupal\external_content\Node\ContentNode;

final readonly class ArrayBuildRequest {

  public function __construct(
    public ContentNode $currentAstNode,
    public ArrayElement $currentArrayElement,
    public ArrayExportRequest $exportRequest,
  ) {}

  public function withNewAstNode(ContentNode $new_content_node): self {
    return new self($new_content_node, $this->currentArrayElement, $this->exportRequest);
  }

  public function withNewArrayElement(ArrayElement $new_array_element): self {
    return new self($this->currentAstNode, $new_array_element, $this->exportRequest);
  }

}
