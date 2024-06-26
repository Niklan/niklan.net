<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder\ExternalContent\RenderArray;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Utils\RenderArrayBuilderHelper;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class CodeBlock implements RenderArrayBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof Element);
    $attributes = $node->getAttributes();
    $info = [];

    if ($attributes->hasAttribute('data-info')) {
      $info = \json_decode($attributes->getAttribute('data-info'), TRUE);
    }

    return RenderArrayBuilderResult::withRenderArray([
      '#theme' => 'niklan_code_block',
      '#language' => $attributes->getAttribute('data-language'),
      '#highlighted_lines' => $info['highlighted_lines'] ?? NULL,
      '#heading' => $info['header'] ?? NULL,
      '#code' => RenderArrayBuilderHelper::buildChildren(
        node: $node,
        child_builder: $child_builder,
      )->result(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof Element && $node->getTag() === 'pre';
  }

}
