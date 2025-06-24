<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Nodes\ContentNode;

final readonly class HtmlParseRequest {

  public function __construct(
    public \DOMNode $currentHtmlNode,
    public ContentNode $currentAstNode,
    public HtmlContentImportRequest $importRequest,
  ) {}

  public function withNewContentNode(ContentNode $new_content_node): self {
    return new self($this->currentHtmlNode, $new_content_node, $this->importRequest);
  }

  public function withNewHtmlNode(\DOMNode $new_html_node): self {
    return new self($new_html_node, $this->currentAstNode, $this->importRequest);
  }

  public function withNewNodes(\DOMNode $new_html_node, ContentNode $new_content_node): self {
    return new self($new_html_node, $new_content_node, $this->importRequest);
  }

}
