<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\Importer;
use Drupal\external_content\Contract\Importer\ImportRequest;
use Drupal\external_content\Factory\DomDocumentFactory;
use Drupal\external_content\Importer\Html\Parser\HtmlParseRequest;
use Drupal\external_content\DataStructure\Nodes\RootNode;

/**
 * @implements \Drupal\external_content\Contract\Importer\Importer<\Drupal\external_content\Importer\Html\HtmlImportRequest>
 */
final readonly class HtmlImporter implements Importer {

  private DomDocumentFactory $domDocumentFactory;

  public function __construct() {
    $this->domDocumentFactory = new DomDocumentFactory();
  }

  /**
   * @param \Drupal\external_content\Importer\Html\HtmlImportRequest $request
   */
  public function import(ImportRequest $request): RootNode {
    $content_root_node = new RootNode();

    $document = $this->domDocumentFactory->createFromHtml($request->getSource()->getSourceData());
    $body = $document->getElementsByTagName('body')->item(0);
    $request->getHtmlParser()->parseChildren(new HtmlParseRequest($body, $content_root_node, $request));

    return $content_root_node;
  }

}
