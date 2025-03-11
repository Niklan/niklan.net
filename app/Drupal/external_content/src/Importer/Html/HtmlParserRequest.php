<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Node\ContentNode;

final readonly class HtmlParserRequest {

  /**
   * @param \DOMNode $htmlNode
   *   Current HTML node for parsing.
   * @param \Drupal\external_content\Node\ContentNode $contentNode
   *   Current AST node for adding children.
   * @param \Drupal\external_content\Importer\Html\HtmlImportRequest $importRequest
   *   HTML import request and context.
   */
  public function __construct(
    public \DOMNode $htmlNode,
    public ContentNode $contentNode,
    public HtmlImportRequest $importRequest,
  ) {}

  public function withContentNode(ContentNode $new_content_node): self {
    return new self($this->htmlNode, $new_content_node, $this->importRequest);
  }

  public function withHtmlNode(\DOMNode $new_html_node): self {
    return new self($new_html_node, $this->contentNode, $this->importRequest);
  }

}
