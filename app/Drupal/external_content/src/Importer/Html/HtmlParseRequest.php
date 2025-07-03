<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Nodes\Node;

final readonly class HtmlParseRequest {

  public function __construct(
    public \DOMNode $currentHtmlNode,
    public Node $currentAstNode,
    public HtmlImportRequest $importRequest,
  ) {}

  public function withNewContentNode(Node $new_content_node): self {
    return new self($this->currentHtmlNode, $new_content_node, $this->importRequest);
  }

  public function withNewHtmlNode(\DOMNode $new_html_node): self {
    return new self($new_html_node, $this->currentAstNode, $this->importRequest);
  }

  public function withNewNodes(\DOMNode $new_html_node, Node $new_content_node): self {
    return new self($new_html_node, $new_content_node, $this->importRequest);
  }

}
