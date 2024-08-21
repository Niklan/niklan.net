<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Drupal\niklan\Entity\Node\BlogEntry;
use Drupal\niklan\Entity\Node\NodeInterface;
use Drupal\niklan\Helper\TocBuilder;

final readonly class NodePreprocess {

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

    match ($variables['view_mode']) {
      default => NULL,
      'full' => $this->addBlogEntryFullVariables($node, $variables),
    };
  }

  private function addBlogEntryFullVariables(BlogEntry $node, array &$variables): void {
    if ($node->get('external_content')->isEmpty()) {
      return;
    }

    $content = $node->get('external_content')->first();
    \assert($content instanceof ExternalContentFieldItem);

    $toc_builder = new TocBuilder();
    $variables['toc_links'] = $toc_builder->getTree($content);
  }

  public function __invoke(array &$variables): void {
    $node = $variables['node'];
    \assert($node instanceof NodeInterface);

    $this->addCommonVariables($node, $variables);

    match ($node::class) {
      default => NULL,
      BlogEntry::class => $this->addBlogEntryVariables($node, $variables),
    };
  }

}
