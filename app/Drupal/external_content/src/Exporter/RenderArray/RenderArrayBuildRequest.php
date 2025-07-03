<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

final readonly class RenderArrayBuildRequest {

  public function __construct(
    public Node $node,
    public RenderArray $renderArray,
    public RenderArrayExportRequest $request,
  ) {}

  public function withNewNode(Node $new_content_node): self {
    return new self($new_content_node, $this->renderArray, $this->request);
  }

  public function withNewRenderArray(RenderArray $render_array): self {
    return new self($this->node, $render_array, $this->request);
  }

  public function withNewNodeAndRenderArray(Node $new_content_node, RenderArray $render_array): self {
    return new self($new_content_node, $render_array, $this->request);
  }

}
