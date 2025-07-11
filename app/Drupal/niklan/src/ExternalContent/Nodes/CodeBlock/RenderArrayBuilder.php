<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\niklan\ExternalContent\Nodes\CodeBlock\CodeBlock>
 */
final readonly class RenderArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof CodeBlock;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $info = \json_decode($request->node->attributes['data-info'] ?? '');
    return new RenderArray([
      '#type' => 'component',
      '#component' => 'niklan:code-block',
      '#props' => [
        'language' => $request->node->attributes['data-language'],
        'highlighted_lines' => $info->highlighted_lines ?? NULL,
        'heading' => $info->header ?? NULL,
        'code' => $request->node->code,
      ],
    ]);
  }

}
