<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\HtmlParserFacadeInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Event\HtmlPostParseEvent;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\external_content\Node\ExternalContentDocument;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides an external HTML parser.
 */
final class HtmlParserFacade implements HtmlParserFacadeInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function parse(ExternalContentFile $file): ExternalContentDocument {
    $html = new ExternalContentHtml($file, $file->getContents());

    $event = new HtmlPreParseEvent($html);
    $this->environment->dispatch($event);

    $document = new ExternalContentDocument($file);
    $crawler = new Crawler($html->getContent());
    $crawler = $crawler->filter('body');
    $body_node = $crawler->getNode(0);
    $this->parseChildren($body_node, $document);

    $event = new HtmlPostParseEvent($document);
    $this->environment->dispatch($event);

    return $document;
  }

  /**
   * Parse children of provided element.
   *
   * @param \DOMNode $element
   *   The element to parse.
   * @param \Drupal\external_content\Contract\Node\NodeInterface $parent
   *   The parent where parsed element goes as children.
   */
  protected function parseChildren(\DOMNode $element, NodeInterface $parent): void {
    foreach ($element->childNodes as $node) {
      $result = $this->parseNode($node);

      if (!$result->hasReplacement()) {
        continue;
      }

      $child = $result->getReplacement();
      $parent->addChild($child);

      if (!$result->shouldContinue()) {
        continue;
      }

      $this->parseChildren($node, $child);
    }
  }

  /**
   * Parse a single node.
   *
   * @param \DOMNode $node
   *   The element to parse.
   */
  protected function parseNode(\DOMNode $node): HtmlParserResult {
    foreach ($this->environment->getHtmlParsers() as $parser) {
      \assert($parser instanceof HtmlParserInterface);
      $result = $parser->parse($node);

      if ($result->hasReplacement() || !$result->shouldContinue()) {
        return $result;
      }
    }

    return HtmlParserResult::continue();
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}