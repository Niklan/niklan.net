<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Contract\Transformer\HtmlNodeTransformer;
use Drupal\external_content\Contract\Transformer\Transformer;
use Drupal\external_content\Contract\Transformer\TransformerContext;
use Drupal\external_content\Utils\PrioritizedList;
use Drupal\external_content\Node\RootNode;
use Symfony\Component\DomCrawler\Crawler;

final readonly class HtmlTransformer implements Transformer {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\Transformer\HtmlNodeTransformer>
   */
  private PrioritizedList $transformers;

  public function __construct() {
    $this->transformers = new PrioritizedList();
  }

  public function addTransformer(HtmlNodeTransformer $transformer, int $priority = 0): void {
    $this->transformers->add($transformer, $priority);
  }

  /**
   * @param \Drupal\external_content\Transformer\Html\HtmlTransformerContext $context
   */
  public function transform(TransformerContext $context): RootNode {
    $html_children_transformer = new HtmlNodeChildrenTransformer($this->transformers);
    $context->setHtmlNodeChildrenTransformer($html_children_transformer);

    $content_root_node = new RootNode();
    $this->parseHtml($content_root_node, $context);

    return $content_root_node;
  }

  private function parseHtml(RootNode $content_root_node, HtmlTransformerContext $context): void {
    $crawler = new Crawler($context->rawHtmlContent);
    $crawler = $crawler->filter('body');
    $html_body = $crawler->getNode(0);
    \assert($html_body instanceof \DOMNode);
    $context->getHtmNodeChildrenTransformer()->transformChildren($html_body, $content_root_node, $context);
  }

}
