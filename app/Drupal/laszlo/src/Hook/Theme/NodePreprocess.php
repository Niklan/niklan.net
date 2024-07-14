<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\niklan\Entity\Node\BlogEntry;
use Drupal\niklan\Entity\Node\NodeInterface;

final readonly class NodePreprocess {

  public function __invoke(array &$variables): void {
    $node = $variables['node'];
    \assert($node instanceof NodeInterface);

    $this->addCommonVariables($node, $variables);

    match ($node::class) {
      default => NULL,
      BlogEntry::class => $this->addBlogEntryVariables($node, $variables),
    };
  }

  private function addCommonVariables(NodeInterface $node, array &$variables): void {
    $variables['url_absolute'] = $node->toUrl()->setAbsolute()->toString();
    $variables['published_timestamp'] = $node->getCreatedTime();
    $variables['comment_count'] = $node
      ->get('comment_node_blog_entry')
      ->first()
      ->get('comment_count')
      ->getValue();
  }

  private function addBlogEntryVariables(BlogEntry $node, array &$variables): void {
    $variables['estimated_read_time'] = $node->getEstimatedReadTime();
  }

}
