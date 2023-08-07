<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Builder;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\Node\HtmlElement;

/**
 * Provides a simple HTML render array builder.
 */
final class HtmlBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, array $children): BuilderResultInterface {
    if (!$node instanceof HtmlElement) {
      return BuilderResult::none();
    }

    return BuilderResult::renderArray([
      '#theme' => 'html_tag',
      '#tag' => $node->getTag(),
      'children' => $children,
    ]);
  }

}
