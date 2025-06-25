<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer\Array;

use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

interface Parser {

  public function supports(ArrayParseRequest $request): bool;

  public function parse(ArrayParseRequest $request): Content;

}
