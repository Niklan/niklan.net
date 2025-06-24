<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ContentImporter;
use Drupal\external_content\Contract\Importer\ContentImportRequest;
use Drupal\external_content\Factory\DomDocumentFactory;
use Drupal\external_content\Nodes\RootNode;

/**
 * @implements \Drupal\external_content\Contract\Importer\ContentImporter<\Drupal\external_content\Importer\Html\HtmlContentImportRequest>
 */
final readonly class HtmlContentImporter implements ContentImporter {

  private DomDocumentFactory $domDocumentFactory;

  public function __construct() {
    $this->domDocumentFactory = new DomDocumentFactory();
  }

  /**
   * @param \Drupal\external_content\Importer\Html\HtmlContentImportRequest $request
   */
  public function import(ContentImportRequest $request): RootNode {
    $content_root_node = new RootNode();

    $document = $this->domDocumentFactory->createFromHtml($request->getSource()->getSourceData());
    $body = $document->getElementsByTagName('body')->item(0);
    $request->getHtmlParser()->parseChildren(new HtmlParseRequest($body, $content_root_node, $request));

    return $content_root_node;
  }

}
