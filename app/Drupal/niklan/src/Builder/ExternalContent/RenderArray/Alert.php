<?php

declare(strict_types=1);

namespace Drupal\niklan\Builder\ExternalContent\RenderArray;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Utils\RenderArrayBuilderHelper;
use Drupal\niklan\Node\ExternalContent\Alert as AlertNode;

/**
 * @ingroup content_sync
 */
final class Alert implements RenderArrayBuilderInterface {

  #[\Override]
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof AlertNode);

    return RenderArrayBuilderResult::withRenderArray([
      '#theme' => 'niklan_alert',
      '#type' => $node->type,
      '#heading' => $node->heading ? $child_builder->build($node->heading)->result() : NULL,
      '#content' => $node->hasChildren() ? RenderArrayBuilderHelper::buildChildren($node, $child_builder)->result() : NULL,
    ]);
  }

  #[\Override]
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof AlertNode;
  }

}
