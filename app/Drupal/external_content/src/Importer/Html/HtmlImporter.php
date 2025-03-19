<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\Importer;
use Drupal\external_content\Contract\Importer\ImportRequest;
use Drupal\external_content\Importer\Html\Parser\HtmlParseRequest;
use Drupal\external_content\Node\RootNode;

/**
 * @implements \Drupal\external_content\Contract\Importer\Importer<\Drupal\external_content\Importer\Html\HtmlImportRequest>
 */
final readonly class HtmlImporter implements Importer {

  /**
   * @param \Drupal\external_content\Importer\Html\HtmlImportRequest $request
   */
  public function import(ImportRequest $request): RootNode {
    // @todo Replace by HTMLDocument in PHP 8.4+.
    // @see https://www.php.net/manual/ru/domdocument.loadhtml.php
    $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    $html .= $request->getSource()->getSourceData();

    $dom = new \DOMDocument('1.0', 'UTF-8');
    @$dom->loadHTML($html, \LIBXML_NOERROR | \LIBXML_NOWARNING);
    $html_body = $dom->getElementsByTagName('body')->item(0);

    $content_root_node = new RootNode();
    $request->getHtmlParser()->parseChildren(new HtmlParseRequest($html_body, $content_root_node, $request));

    return $content_root_node;
  }

}
