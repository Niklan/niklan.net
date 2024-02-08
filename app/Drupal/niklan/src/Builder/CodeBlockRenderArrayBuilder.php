<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\Node\Html\Element;

/**
 * {@selfdoc}
 */
final class CodeBlockRenderArrayBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface {
    return BuilderResult::renderArray([
      '#markup' => '@TODO COMPLETE',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return $type === RenderArrayBuilder::class && $node instanceof Element && $node->getTag() === 'pre';
  }

}
