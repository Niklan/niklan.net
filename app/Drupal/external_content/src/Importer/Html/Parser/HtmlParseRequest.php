<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Importer\Html\HtmlImportRequest;
use Drupal\external_content\DataStructure\Nodes\ContentNode;

final readonly class HtmlParseRequest {

  public function __construct(
    public \DOMNode $currentHtmlNode,
    public ContentNode $currentAstNode,
    public HtmlImportRequest $importRequest,
  ) {}

  public function withNewContentNode(ContentNode $new_content_node): self {
    return new self($this->currentHtmlNode, $new_content_node, $this->importRequest);
  }

  public function withNewHtmlNode(\DOMNode $new_html_node): self {
    return new self($new_html_node, $this->currentAstNode, $this->importRequest);
  }

}
