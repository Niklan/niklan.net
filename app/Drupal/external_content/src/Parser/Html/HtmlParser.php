<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Exception\UnsupportedElementException;
use Drupal\external_content\Factory\DomDocumentFactory;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\Registry;

final readonly class HtmlParser implements Parser, ChildParser {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Parser\Html\Parser> $parsers
   */
  public function __construct(
    private Registry $parsers,
  ) {}

  public function parse(string $html): Document {
    $content_document = new Document();

    // @todo DI
    $dom_document_factory = new DomDocumentFactory();
    $html_document = $dom_document_factory->createFromHtml($html);
    $body = $html_document->getElementsByTagName('body')->item(0);
    $this->parseChildren($body, $content_document);

    return $content_document;
  }

  public function parseChildren(\DOMNode $html_node, Node $content_node): void {
    foreach ($html_node->childNodes as $child_html_node) {
      $content_node->addChild($this->parseElement($child_html_node, $this));
    }
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    foreach ($this->parsers->getAll() as $parser) {
      if (!$parser->supports($dom_node)) {
        continue;
      }
      return $parser->parseElement($dom_node, $child_parser);
    }

    throw new UnsupportedElementException(self::class, self::domNodeToString($dom_node));
  }

  public function supports(\DOMNode $dom_node): bool {
    return TRUE;
  }

  private static function domNodeToString(\DOMNode $node): string {
    $document = new \DOMDocument();
    $cloned_node = $document->importNode($node->cloneNode(TRUE), TRUE);
    $document->appendChild($cloned_node);
    $content = \trim($document->saveHTML());

    return $content === '' ? '[empty]' : $content;
  }

}
