<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\Importer;
use Drupal\external_content\Contract\Importer\ImportRequest;
use Drupal\external_content\Importer\Html\Parser\HtmlParseRequest;
use Drupal\external_content\DataStructure\Nodes\RootNode;

/**
 * @implements \Drupal\external_content\Contract\Importer\Importer<\Drupal\external_content\Importer\Html\HtmlImportRequest>
 */
final readonly class HtmlImporter implements Importer {

  /**
   * @param \Drupal\external_content\Importer\Html\HtmlImportRequest $request
   */
  public function import(ImportRequest $request): RootNode {
    $document = $this->createDomDocument($request->getSource()->getSourceData());
    $body = $document->getElementsByTagName('body')->item(0);

    return $this->parseDomBody($body, $request);
  }

  private function createDomDocument(string $html): \DOMDocument {
    // @todo Replace by HTMLDocument in PHP 8.4+.
    // @see https://www.php.net/manual/ru/domdocument.loadhtml.php
    $charset = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    $document = new \DOMDocument('1.0', 'UTF-8');
    @$document->loadHTML($charset . $html, \LIBXML_NOERROR | \LIBXML_NOWARNING);

    return $document;
  }

  private function parseDomBody(\DOMElement $body, HtmlImportRequest $request): RootNode {
    $content_root_node = new RootNode();
    $request->getHtmlParser()->parseChildren(new HtmlParseRequest($body, $content_root_node, $request));

    return $content_root_node;
  }

}
