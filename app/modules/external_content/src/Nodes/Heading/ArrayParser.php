<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Parser\Array\ChildParser;
use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Nodes\Node;

/**
 * @deprecated Exactly the same as HtmlElement.
 */
final readonly class ArrayParser implements Parser {

  public function supports(ArrayElement $array): bool {
    return $array->type === Heading::getNodeType();
  }

  public function parseElement(ArrayElement $array, ChildParser $child_parser): Node {
    $node = new Heading(HeadingTagType::from($array->properties['tag']));
    $child_parser->parseChildren($array, $node);
    return $node;
  }

}
