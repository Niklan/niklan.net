<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\DataStructure;

use Drupal\external_content\Importer\Array\Parser\ArrayParseRequest;
use Drupal\external_content\DataStructure\Nodes\ContentNode;

interface ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool;

  public function parse(ArrayParseRequest $request): ContentNode;

}
