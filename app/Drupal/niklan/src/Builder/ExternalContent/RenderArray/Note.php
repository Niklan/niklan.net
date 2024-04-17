<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder\ExternalContent\RenderArray;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\niklan\Node\ExternalContent\Note as NoteNode;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class Note implements RenderArrayBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof NoteNode);

    return RenderArrayBuilderResult::withRenderArray([
      '#markup' => '@TODO NOTE THEME HOOK',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof NoteNode;
  }

}
