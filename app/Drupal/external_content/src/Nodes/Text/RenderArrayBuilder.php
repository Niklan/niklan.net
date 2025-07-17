<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\Component\Utility\Html;
use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\external_content\Nodes\Text\Text>
 */
final readonly class RenderArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof Text;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    // Escape HTML to prevent tag misinterpretation and DOM injection. Ensures:
    // - '<ul>' renders as text, not HTML element
    // - Complies with Drupal security standards
    // Example: Unescaped '<code><ul></code>' becomes broken
    // '<code></code><ul></ul>'.
    return new RenderArray(['#markup' => Html::escape($node->text)]);
  }

}
