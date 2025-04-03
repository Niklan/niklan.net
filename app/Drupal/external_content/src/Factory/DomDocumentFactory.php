<?php

namespace Drupal\external_content\Factory;

final readonly class DomDocumentFactory {

  public function createFromHtml(string $html): \DOMDocument {
    $document = new \DOMDocument('1.0', 'UTF-8');
    @$document->loadHTML($this->wrapHtml($html), \LIBXML_NOERROR | \LIBXML_NOWARNING);

    return $document;
  }

  private function wrapHtml(string $html): string {
    return '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html;
  }

}
