<?php

declare(strict_types=1);

namespace Drupal\niklan\Markup\Markdown\Renderer;

use Drupal\niklan\Markup\Markdown\Node\BlockDirective;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * @ingroup markdown
 */
abstract class BlockDirectiveRenderer implements NodeRendererInterface {

  abstract protected function directiveSelector(): string;

  #[\Override]
  public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable {
    \assert($node instanceof BlockDirective);

    return new HtmlElement(
      tagName: 'div',
      attributes: $this->prepareElementAttributes($node),
      contents: $this->prepareContents($node, $childRenderer),
    );
  }

  private function prepareElementAttributes(BlockDirective $node): array {
    $attributes = [
      'data-selector' => $this->directiveSelector(),
      'data-type' => $node->type,
    ];

    if ($node->argument) {
      $attributes['data-argument'] = $node->argument;
    }

    if ($node->attributes) {
      $attributes += $node->attributes;
    }

    return \array_filter($attributes);
  }

  private function prepareContents(BlockDirective $node, ChildNodeRendererInterface $childRenderer): string {
    $contents = $this->wrapContents(
      selector: 'inline-content',
      node: $node->inlineContent,
      childRenderer: $childRenderer,
    );
    $contents .= $this->wrapContents(
      selector: 'content',
      node: $node,
      childRenderer: $childRenderer,
    );

    return $contents;
  }

  private function wrapContents(string $selector, Node $node, ChildNodeRendererInterface $childRenderer): string {
    if (!$node->hasChildren()) {
      return '';
    }

    return (string) new HtmlElement(
      tagName: 'div',
      attributes: ['data-selector' => $selector],
      contents: $childRenderer->renderNodes($node->children()),
    );
  }

}
