<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Utils\PrioritizedList;

final readonly class HtmlParser {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\Importer\HtmlNodeParser>
   */
  private PrioritizedList $parsers;

  public function __construct() {
    $this->parsers = new PrioritizedList();
  }

  public function addParser(HtmlNodeParser $parser, int $priority = 0): void {
    $this->parsers->add($parser, $priority);
  }

  public function parseChildren(\DOMNode $html_node, ContentNode $content_node, HtmlImporterContext $context): void {
    foreach ($html_node->childNodes as $child_html_node) {
      $this->parseChild($child_html_node, $content_node, $context);
    }
  }

  private function parseChild(\DOMNode $html_node, ContentNode $content_node, HtmlImporterContext $context): void {
    foreach ($this->parsers as $transformer) {
      if ($transformer->supports($html_node, $context)) {
        $content_node->addChild($transformer->parse($html_node, $context));

        return;
      }
    }

    $context->getLogger()->error("No HTML parser found for node: {$html_node->nodeName}");
  }

}
