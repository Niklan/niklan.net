<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\Node\HtmlElement;

/**
 * Provides a simple HTML render array builder.
 */
final class HtmlElementBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, array $children): BuilderResultInterface {
    if (!$node instanceof HtmlElement) {
      return BuilderResult::none();
    }

    return BuilderResult::renderArray([
      '#type' => 'html_tag',
      '#tag' => $node->getTag(),
      '#attributes' => $node->getAttributes()->all(),
      'children' => $children,
    ]);
  }

}
