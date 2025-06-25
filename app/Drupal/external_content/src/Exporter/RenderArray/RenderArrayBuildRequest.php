<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Content\Content;

final readonly class RenderArrayBuildRequest {

  public function __construct(
    public Content $currentAstNode,
    public RenderArray $renderArray,
    public RenderArrayExportRequest $exportRequest,
  ) {}

  public function withNewAstNode(Content $new_content_node): self {
    return new self($new_content_node, $this->renderArray, $this->exportRequest);
  }

  public function withNewRenderArray(RenderArray $render_array): self {
    return new self($this->currentAstNode, $render_array, $this->exportRequest);
  }

}
