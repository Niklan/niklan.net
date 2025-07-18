<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser\Array;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

interface Parser {

  public function supports(ArrayElement $array): bool;

  public function parseElement(ArrayElement $array, ChildParser $child_parser): Node;

}
