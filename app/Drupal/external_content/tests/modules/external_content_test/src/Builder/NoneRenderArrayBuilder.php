<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Builder;

use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;

/**
 * {@selfdoc}
 */
final class NoneRenderArrayBuilder implements RenderArrayBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface {
    return BuilderResult::none();
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return TRUE;
  }

}
