<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Contract\Parser\Transformer;
use Drupal\external_content\Contract\Transformer\NodeHtmlTransformer;
use Drupal\external_content\Contract\Transformer\TransformerContext;
use Drupal\external_content\Utils\PrioritizedList;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\RootNode;
use Symfony\Component\DomCrawler\Crawler;

final readonly class HtmlTransformer implements Transformer {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\Transformer\NodeHtmlTransformer>
   */
  private PrioritizedList $transformers;

  public function __construct() {
    $this->transformers = new PrioritizedList();
  }

  public function addTransformer(NodeHtmlTransformer $transformer, int $priority = 0): void {
    $this->transformers->add($transformer, $priority);
  }

  /**
   * @param \Drupal\external_content\Transformer\Html\HtmlTransformerContext $context
   */
  public function transform(TransformerContext $context): RootNode {
    $content_root_node = new RootNode();
    $this->parseHtml($content_root_node, $context);

    return $content_root_node;
  }

  private function parseHtml(RootNode $content_root_node, HtmlTransformerContext $context): void {
    $crawler = new Crawler($context->rawHtmlContent);
    $crawler = $crawler->filter('body');
    $html_body = $crawler->getNode(0);
    \assert($html_body instanceof \DOMNode);
    $this->parseChildren($html_body, $content_root_node, $context);
  }

  private function parseChildren(\DOMNode $html_node, ContentNode $content_node, HtmlTransformerContext $context): void {
    foreach ($html_node->childNodes as $child_html_node) {
      $this->parseChild($child_html_node, $content_node, $context);
    }
  }

  private function parseChild(\DOMNode $html_node, ContentNode $content_node, HtmlTransformerContext $context): void {
    foreach ($this->transformers as $transformer) {
      if ($transformer->supports($html_node, $context)) {
        $content_node->addChild($transformer->transform($html_node, $context));
        break;
      }
    }

    $context->getLogger()->error("No HTML transformer found for node: {$html_node->nodeName}");
    throw new \RuntimeException("No HTML transformer found for node: " . $html_node->nodeName);
  }

}
