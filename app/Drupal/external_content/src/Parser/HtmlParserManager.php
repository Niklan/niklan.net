<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserManagerInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\Html;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides an external HTML parser.
 */
final class HtmlParserManager implements HtmlParserManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ContainerInterface $container,
    private array $htmlParsers = [],
  ) {}

  /**
   * {@selfdoc}
   */
  public function parse(Html $html, EnvironmentInterface $environment): Content {
    $pre_parse_event = new HtmlPreParseEvent($html->contents(), $html->data());
    $environment->dispatch($pre_parse_event);

    $content = new Content($pre_parse_event->data);
    $crawler = new Crawler($pre_parse_event->content);
    $crawler = $crawler->filter('body');
    $body_node = $crawler->getNode(0);
    $this->parseChildren($body_node, $content, $environment);

    return $content;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function get(string $parser_id): HtmlParserInterface {
    if (!$this->has($parser_id)) {
      throw new MissingContainerDefinitionException(
        type: 'html_parser',
        id: $parser_id,
      );
    }

    $service = $this->htmlParsers[$parser_id]['service'];

    return $this->container->get($service);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function has(string $parser_id): bool {
    return \array_key_exists($parser_id, $this->htmlParsers);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function list(): array {
    return $this->htmlParsers;
  }

  /**
   * {@selfdoc}
   */
  protected function parseChildren(\DOMNode $element, NodeInterface $parent, EnvironmentInterface $environment): void {
    foreach ($element->childNodes as $node) {
      $result = $this->parseNode($node, $environment);

      if ($result->hasNoReplacement()) {
        continue;
      }

      $parent->addChild($result->replacement());

      if (!$result->shouldContinue()) {
        continue;
      }

      $this->parseChildren($node, $result->replacement(), $environment);
    }
  }

  /**
   * {@selfdoc}
   */
  protected function parseNode(\DOMNode $node, EnvironmentInterface $environment): HtmlParserResult {
    foreach ($environment->getHtmlParsers() as $parser) {
      \assert($parser instanceof HtmlParserInterface);
      $result = $parser->parseNode($node);

      if ($result->hasReplacement() || $result->shouldNotContinue()) {
        return $result;
      }
    }

    return HtmlParserResult::pass();
  }

}
