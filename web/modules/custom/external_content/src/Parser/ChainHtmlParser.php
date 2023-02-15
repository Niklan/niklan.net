<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\Component\Utility\SortArray;
use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Dto\HtmlParserStateInterface;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a chained HTML parser.
 */
final class ChainHtmlParser implements ChainHtmlParserInterface {

  /**
   * The array with instantiated HTML parsers.
   *
   * @var \Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserInterface[]
   */
  protected array $parsers = [];

  /**
   * Constructs a new ChainHtmlParser object.
   *
   * @param \Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManagerInterface $htmlParserPluginManager
   *   The HTML parser plugin manager.
   */
  public function __construct(
    protected HtmlParserPluginManagerInterface $htmlParserPluginManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function parseRoot(string $html, HtmlParserStateInterface $html_parser_state): SourceFileContent {
    $this->initParsers();

    $content = new SourceFileContent();
    $crawler = new Crawler($html);
    $crawler = $crawler->filter('body');

    foreach ($crawler->children() as $child) {
      $element = $this->parseElement($child, $html_parser_state);

      if (!$element) {
        continue;
      }

      $content->addChild($element);
    }

    return $content;
  }

  /**
   * Instantiates HTML parsers.
   */
  protected function initParsers(): void {
    if (\count($this->parsers)) {
      return;
    }

    $definitions = $this->htmlParserPluginManager->getDefinitions();
    \uasort($definitions, [SortArray::class, 'sortByWeightElement']);

    foreach (\array_keys($definitions) as $parser_id) {
      $this->parsers[$parser_id] = $this
        ->htmlParserPluginManager
        ->createInstance($parser_id);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function parseElement(\DOMNode $node, HtmlParserStateInterface $html_parser_state): ?ElementInterface {
    // Make sure parsers instantiated even here, because this method can be
    // called before or without ::parseRoot() on DOM element directly. This most
    // likely happens only for testing.
    $this->initParsers();

    // This is done for PHPStan that requires explicit return outside foreach.
    $element = NULL;

    foreach ($this->parsers as $parser) {
      if ($parser::isApplicable($node)) {
        $element = $parser->parse($node, $html_parser_state);

        break;
      }
    }

    return $element;
  }

}
