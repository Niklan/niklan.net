<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\RenderArrayBuilder;

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

    $build = [
      '#type' => 'component',
      '#component' => 'niklan:alert',
      '#props' => [
        'type' => $node->type,
      ],
    ];

    if ($node->heading) {
      $build['#slots']['heading'] = $child_builder
        ->build($node->heading)
        ->result();
    }

    if ($node->hasChildren()) {
      $build['#slots']['content'] = RenderArrayBuilderHelper::buildChildren(
        node: $node,
        child_builder: $child_builder,
      )->result();
    }

    return RenderArrayBuilderResult::withRenderArray($build);
  }

  #[\Override]
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof AlertNode;
  }

}
