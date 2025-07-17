<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Node;

/**
 * @implements \Drupal\external_content\Contract\Builder\RenderArray\Builder<\Drupal\niklan\ExternalContent\Nodes\Callout\Callout>
 */
final readonly class RenderArrayBuilder implements Builder {

  public function supports(Node $node): bool {
    return $node instanceof Callout;
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
    $build = [
      '#type' => 'component',
      '#component' => 'niklan:callout',
      '#props' => [
        'type' => $node->type,
      ],
    ];

    $title = $this->prepareTitle($node, $child_builder);
    if ($title) {
      $build['#slots']['title'] = $title;
    }

    $body = $this->prepareBody($node, $child_builder);
    if ($body) {
      $build['#slots']['body'] = $body;
    }

    return new RenderArray($build);
  }

  private function prepareBody(Callout $node, ChildBuilder $child_builder): ?array {
    if (!$node->getBody()) {
      return NULL;
    }

    $result = new RenderArray();
    $child_builder->buildChildren($node->getBody(), $result);
    return $result->toRenderArray();
  }

  private function prepareTitle(Callout $node, ChildBuilder $child_builder): ?array {
    if (!$node->getTitle()) {
      return NULL;
    }

    $result = new RenderArray();
    $child_builder->buildChildren($node->getTitle(), $result);
    return $result->toRenderArray();
  }

}
