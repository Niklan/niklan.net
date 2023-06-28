<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\ExternalContentHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Node\ExternalContentDocument;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides an external HTML parser.
 */
final class ExternalContentHtmlParser implements ExternalContentHtmlParserInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * Constructs a new ExternalContentHtmlParser instance.
   *
   * @param \Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected EnvironmentAwareClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function parse(ExternalContentFile $file): ExternalContentDocument {
    $html = new ExternalContentHtml($file, $file->getContents());
    // @todo pre parse.
    $document = new ExternalContentDocument($file);

    $crawler = new Crawler($html->getContent());
    $crawler = $crawler->filter('body');
    $body_node = $crawler->getNode(0);

    $this->parseChildren($body_node, $document);

    // @todo post parse.
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
      $instance = $this->classResolver->getInstance(
        $parser,
        HtmlParserInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof HtmlParserInterface);
      $result = $instance->parse($node);

      if ($result->hasReplacement() || !$result->shouldContinue()) {
        return $result;
      }
    }

    return HtmlParserResult::continue();
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
