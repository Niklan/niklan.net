<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Array;

use Drupal\external_content\Contract\Parser\Array\ChildParser;
use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exception\UnsupportedElementException;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\Registry;

final readonly class ArrayParser implements Parser, ChildParser {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Parser\Array\Parser> $parsers
   */
  public function __construct(
    private Registry $parsers,
  ) {}

  public function parse(ArrayElement $array): Document {
    \assert(self::supports($array), 'Array element must represent a document');
    $document = new Document();
    $this->parseChildren($array, $document);
    return $document;
  }

  public function parseChildren(ArrayElement $parent_array, Node $content_node): void {
    foreach ($parent_array->getChildren() as $array) {
      $child_node = $this->parseElement($array, $this);
      if (!$child_node) {
        continue;
      }
      $content_node->addChild($child_node);
    }
  }

  public function parseElement(ArrayElement $array, ChildParser $child_parser): Node {
    foreach ($this->parsers->getAll() as $parser) {
      if (!$parser->supports($array)) {
        continue;
      }

      $node = $parser->parse($array, $child_parser);
      if (!$node) {
        continue;
      }

      $child_parser->parseChildren($array, $node);
      return $node;
    }

    throw new UnsupportedElementException(self::class, $array->type);
  }

  public function supports(ArrayElement $array): bool {
    return $array->type === Document::getNodeType();
  }

}
