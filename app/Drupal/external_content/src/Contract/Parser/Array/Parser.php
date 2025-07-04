<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser\Array;

use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Array\ArrayParseRequest;

interface Parser {

  public function supports(ArrayParseRequest $request): bool;

  public function parse(ArrayParseRequest $request): Node;

}
