<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder\Html;

use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\Node\Html\Element;

/**
 * Provides a simple HTML render array builder.
 */
final class ElementRenderArrayBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type, array $context = []): BuilderResultInterface {
    return BuilderResult::renderArray([
      '#type' => 'html_tag',
      '#tag' => $node->getTag(),
      '#attributes' => $node->getAttributes()->all(),
      'children' => $context['children'],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return $type === RenderArrayBuilder::class && $node instanceof Element;
  }

}
