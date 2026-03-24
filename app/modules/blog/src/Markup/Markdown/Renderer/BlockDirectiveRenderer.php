<?php

declare(strict_types=1);

namespace Drupal\app_blog\Markup\Markdown\Renderer;

use Drupal\app_blog\Markup\Markdown\Node\BlockDirective;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * @ingroup markdown
 */
abstract class BlockDirectiveRenderer implements NodeRendererInterface {

  #[\Override]
  public function render(Node $node, ChildNodeRendererInterface $child_renderer): \Stringable {
    \assert($node instanceof BlockDirective);

    return new HtmlElement(
      tagName: 'div',
      attributes: $this->prepareElementAttributes($node),
      contents: $this->prepareContents($node, $child_renderer),
    );
  }

  abstract protected function directiveSelector(): string;

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

    // It is important to filter out only NULL values and empty arrays. An empty
    // string is a valid value for attributes like 'autoplay', 'loop',
    // 'controls', etc.
    return \array_filter(
      array: $attributes,
      callback: static fn ($value) => !(\is_null($value) || (\is_array($value) && \count($value) === 0)),
    );
  }

  private function prepareContents(BlockDirective $node, ChildNodeRendererInterface $child_renderer): string {
    $contents = $this->wrapContents(
      selector: 'inline-content',
      node: $node->inlineContent,
      child_renderer: $child_renderer,
    );
    $contents .= $this->wrapContents(
      selector: 'content',
      node: $node,
      child_renderer: $child_renderer,
    );

    return $contents;
  }

  private function wrapContents(string $selector, Node $node, ChildNodeRendererInterface $child_renderer): string {
    if (!$node->hasChildren()) {
      return '';
    }

    return (string) new HtmlElement(
      tagName: 'div',
      attributes: ['data-selector' => $selector],
      contents: $child_renderer->renderNodes($node->children()),
    );
  }

}
