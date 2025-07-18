<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Contract\Parser\Array\ChildParser;
use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayElement $array): bool {
    return $array->type === Text::getNodeType();
  }

  public function parseElement(ArrayElement $array, ChildParser $child_parser): Node {
    return new Text($array->properties['text']);
  }

}
