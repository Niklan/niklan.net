<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\CalloutBody;

use Drupal\external_content\Contract\Parser\Array\ChildParser;
use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Node;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayElement $array): bool {
    return $array->type === CalloutBody::getNodeType();
  }

  public function parseElement(ArrayElement $array, ChildParser $child_parser): Node {
    $node = new CalloutBody();
    $child_parser->parseChildren($array, $node);
    return $node;
  }

}
