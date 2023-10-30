<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\Html\HtmlParserInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\Content;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides an external HTML parser.
 */
final class HtmlParser implements ParserInterface, EnvironmentAwareInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsParse(SourceInterface $source): bool {
    return $source->type() === 'html';
  }

  /**
   * {@selfdoc}
   */
  public function parse(SourceInterface $source): Content {
    $document = new Content($source);
    $crawler = new Crawler($source->contents());
    $crawler = $crawler->filter('body');
    $body_node = $crawler->getNode(0);
    $this->parseChildren($body_node, $document);

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
    foreach ($this->environment->getParsers() as $parser) {
      \assert($parser instanceof HtmlParserInterface);
      $result = $parser->parseNode($node);

      if ($result->hasReplacement() || !$result->shouldContinue()) {
        return $result;
      }
    }

    return HtmlParserResult::continue();
  }

}
