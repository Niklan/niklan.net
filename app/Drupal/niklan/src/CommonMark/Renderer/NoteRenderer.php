<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Renderer;

use Drupal\niklan\CommonMark\Block\Note;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class NoteRenderer implements NodeRendererInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable {
    \assert($node instanceof Note);

    return new HtmlElement(
      tagName: 'div',
      attributes: [
        'data-selector' => 'niklan:note',
        'data-note-type' => $node->type,
      ],
      contents: $childRenderer->renderNodes($node->children()),
    );
  }

}
