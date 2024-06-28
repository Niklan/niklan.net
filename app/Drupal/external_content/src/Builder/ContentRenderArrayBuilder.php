<?php

declare(strict_types=1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Utils\RenderArrayBuilderHelper;

/**
 * {@selfdoc}
 */
final class ContentRenderArrayBuilder implements RenderArrayBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof Content);

    return RenderArrayBuilderHelper::buildChildren($node, $child_builder);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof Content;
  }

}
