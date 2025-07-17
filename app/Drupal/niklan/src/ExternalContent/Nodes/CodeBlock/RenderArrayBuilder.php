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
    /**
     * @var object{highlighted_lines?: string, header?: string} $info
     */
    $info = \json_decode($node->attributes['data-info'] ?? '');
    return new RenderArray([
      '#type' => 'component',
      '#component' => 'niklan:code-block',
      '#props' => [
        'language' => $node->attributes['data-language'],
        'highlighted_lines' => $info->highlighted_lines ?? NULL,
        'heading' => $info->header ?? NULL,
        'code' => $node->code,
      ],
    ]);
  }

}
