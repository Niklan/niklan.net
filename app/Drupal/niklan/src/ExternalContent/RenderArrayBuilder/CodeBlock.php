<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\RenderArrayBuilder;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Utils\RenderArrayBuilderHelper;

/**
 * @ingroup content_sync
 */
final class CodeBlock implements RenderArrayBuilderInterface {

  #[\Override]
  public function build(NodeInterface $node, ChildRenderArrayBuilderInterface $child_builder): RenderArrayBuilderResult {
    \assert($node instanceof Element);
    $attributes = $node->getAttributes();
    $info = [];

    if ($attributes->hasAttribute('data-info')) {
      \assert(\is_string($attributes->getAttribute('data-info')));
      /**
       * @var array{
       *   highlighted_lines: ?non-empty-string,
       *   header: ?non-empty-string,
       * } $info
       */
      $info = \json_decode($attributes->getAttribute('data-info'), TRUE);
    }

    return RenderArrayBuilderResult::withRenderArray([
      '#type' => 'component',
      '#component' => 'niklan:code-block',
      '#props' => [
        'language' => $attributes->getAttribute('data-language'),
        'highlighted_lines' => $info['highlighted_lines'] ?? NULL,
        'heading' => $info['header'] ?? NULL,
        'code' => RenderArrayBuilderHelper::buildChildren(
          node: $node,
          child_builder: $child_builder,
        )->result(),
      ],
    ]);
  }

  #[\Override]
  public function supportsBuild(NodeInterface $node): bool {
    return $node instanceof Element && $node->getTag() === 'pre';
  }

}
