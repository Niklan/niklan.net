<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Renderer;

use Drupal\niklan\CommonMark\Block\ContainerBlockDirective;
use Drupal\niklan\Helper\CommonMarkDirectiveHelper;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class ContainerBlockDirectiveRenderer implements NodeRendererInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable {
    \assert($node instanceof ContainerBlockDirective);

    return new HtmlElement(
      tagName: 'div',
      attributes: [
        'data-selector' => 'niklan:container-directive',
      ] + CommonMarkDirectiveHelper::prepareElementAttributes($node->info),
      contents: $childRenderer->renderNodes($node->children()),
    );
  }

}
