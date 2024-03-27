<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Builder;

use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;

/**
 * {@selfdoc}
 */
final class EmptyRenderArrayBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface {
    return BuilderResult::renderArray([]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return $type === RenderArrayBuilder::class;
  }

}
