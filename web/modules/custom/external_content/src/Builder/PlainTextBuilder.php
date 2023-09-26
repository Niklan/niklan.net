<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\Node\PlainText;

/**
 * Provides a simple builder for the plain text.
 */
final class PlainTextBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, array $children): BuilderResultInterface {
    if (!$node instanceof PlainText) {
      return BuilderResult::none();
    }

    return BuilderResult::renderArray([
      '#markup' => $node->getContent(),
    ]);
  }

}
