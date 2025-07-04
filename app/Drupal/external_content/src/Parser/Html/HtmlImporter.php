<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Parser\ParseRequest;
use Drupal\external_content\Contract\Parser\Parser;
use Drupal\external_content\Factory\DomDocumentFactory;
use Drupal\external_content\Nodes\Document;

/**
 * @implements \Drupal\external_content\Contract\Parser\Parser<\Drupal\external_content\Parser\Html\HtmlParseRequest>
 */
final readonly class HtmlParser implements Parser {

  private DomDocumentFactory $domDocumentFactory;

  public function __construct() {
    $this->domDocumentFactory = new DomDocumentFactory();
  }

  /**
   * @param \Drupal\external_content\Parser\Html\HtmlParseRequest $request
   */
  public function parse(ParseRequest $request): Document {
    $content_root_node = new Document();

    $document = $this->domDocumentFactory->createFromHtml($request->getSource()->getSourceData());
    $body = $document->getElementsByTagName('body')->item(0);
    $request->getHtmlParser()->parseChildren(new HtmlParseRequest($body, $content_root_node, $request));

    return $content_root_node;
  }

}
