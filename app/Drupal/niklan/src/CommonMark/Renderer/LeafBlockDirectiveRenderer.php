<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Renderer;

use Drupal\niklan\CommonMark\Block\LeafBlockDirective;
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
final class LeafBlockDirectiveRenderer implements NodeRendererInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable {
    \assert($node instanceof LeafBlockDirective);

    $element_attributes = [
      'data-selector' => 'niklan:leaf-directive',
    ] + CommonMarkDirectiveHelper::prepareElementAttributes($node->info);

    return new HtmlElement(tagName: 'div', attributes: $element_attributes);
  }

}
