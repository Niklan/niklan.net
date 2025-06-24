<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\ContentImporter;
use Drupal\external_content\Contract\Importer\ContentImportRequest;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\RootNode;

/**
 * @implements \Drupal\external_content\Contract\Importer\ContentImporter<\Drupal\external_content\Importer\Array\ArrayContentImportRequest>
 */
final readonly class ArrayContentImporter implements ContentImporter {

  /**
   * @param \Drupal\external_content\Importer\Array\ArrayContentImportRequest $request
   */
  public function import(ContentImportRequest $request): RootNode {
    $root = new RootNode();

    $array_element = ArrayElement::fromArray($request->getSource()->getSourceData());
    $parse_request = new ArrayParseRequest($array_element, $root, $request);
    $request->getArrayParser()->parseChildren($parse_request);

    return $root;
  }

}
