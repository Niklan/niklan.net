<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Node\PlainText;

/**
 * Provides a simple builder for the plain text.
 */
final class PlainTextRenderArrayBuilder implements RenderArrayBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof PlainText);

    return RenderArrayBuilderResult::withRenderArray([
      '#markup' => $node->getContent(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof PlainText;
  }

}
