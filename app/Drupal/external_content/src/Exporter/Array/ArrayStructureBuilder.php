<?php

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Utils\PrioritizedList;

final class ArrayStructureBuilder {

  private PrioritizedList $parsers;

  public function __construct() {
    $this->parsers = new PrioritizedList();
  }

  public function addBuilder(HtmlNodeParser $parser, int $priority = 0): void {
    $this->parsers->add($parser, $priority);
  }

  public function build(ArrayStructureBuilderRequest $request): ArrayElement {
    // @todo complete.
    $request->exportRequest->getArrayStructureBuilder()->build();
  }

}
