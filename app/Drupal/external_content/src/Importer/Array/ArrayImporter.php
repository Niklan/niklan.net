<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\Importer;
use Drupal\external_content\Contract\Importer\ImportRequest;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Document;

/**
 * @implements \Drupal\external_content\Contract\Importer\Importer<\Drupal\external_content\Importer\Array\ArrayImportRequest>
 */
final readonly class ArrayImporter implements Importer {

  /**
   * @param \Drupal\external_content\Importer\Array\ArrayImportRequest $request
   */
  public function import(ImportRequest $request): Document {
    $root = new Document();

    $array_element = ArrayElement::fromArray($request->getSource()->getSourceData());
    $parse_request = new ArrayParseRequest($array_element, $root, $request);
    $request->getArrayParser()->parseChildren($parse_request);

    return $root;
  }

}
