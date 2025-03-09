<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Utils\PrioritizedList;

final readonly class HtmlNodeChildrenTransformer {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\Transformer\HtmlNodeTransformer> $transformers
   */
  public function __construct(
    private PrioritizedList $transformers,
  ) {}

  public function transformChildren(\DOMNode $html_node, ContentNode $content_node, HtmlTransformerContext $context): void {
    foreach ($html_node->childNodes as $child_html_node) {
      $this->transformChild($child_html_node, $content_node, $context);
    }
  }

  private function transformChild(\DOMNode $html_node, ContentNode $content_node, HtmlTransformerContext $context): void {
    foreach ($this->transformers as $transformer) {
      if ($transformer->supports($html_node, $context)) {
        $content_node->addChild($transformer->transform($html_node, $context));

        return;
      }
    }

    $context->getLogger()->error("No HTML transformer found for node: {$html_node->nodeName}");
  }

}
