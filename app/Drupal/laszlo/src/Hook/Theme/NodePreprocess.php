<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\niklan\Entity\Node\NodeInterface;

final readonly class NodePreprocess {

  public function __invoke(array &$variables): void {
    $node = $variables['node'];
    \assert($node instanceof NodeInterface);

    $this->addCommonVariables($node, $variables);
  }

  private function addCommonVariables(NodeInterface $node, array &$variables): void {
    $variables['published_timestamp'] = $node->getCreatedTime();
    $variables['comment_count'] = $node
      ->get('comment_node_blog_entry')
      ->first()
      ->get('comment_count')
      ->getValue();
  }

}
