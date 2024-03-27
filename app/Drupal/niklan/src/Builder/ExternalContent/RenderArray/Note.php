<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder\ExternalContent\RenderArray;

use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\niklan\Node\ExternalContent\Note as NoteNode;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class Note implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface {
    \assert($node instanceof NoteNode);

    return BuilderResult::renderArray([
      '#markup' => '@TODO NOTE THEME HOOK',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return $type === RenderArrayBuilder::class && $node instanceof NoteNode;
  }

}
