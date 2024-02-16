<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Renderer;

use Drupal\niklan\CommonMark\Block\ContainerDirective;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class ContainerDirectiveRenderer implements NodeRendererInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable {
    \assert($node instanceof ContainerDirective);

    return new HtmlElement(
      tagName: 'div',
      attributes: [
        // @todo Inline content and metadata.
        // @see \Drupal\niklan\CommonMark\Extension\ContainerDirectiveExtension
        'data-selector' => 'niklan:container-directive',
        'data-container-type' => $this->resolveType($node),
      ],
      contents: $childRenderer->renderNodes($node->children()),
    );
  }

  /**
   * {@selfdoc}
   */
  private function resolveType(ContainerDirective $node): string {
    \dump($node->info);
    $matches = RegexHelper::matchFirst('/^\s*([a-z]+)/', $node->info);

    return $matches[1];
  }

}
