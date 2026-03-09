<?php

declare(strict_types=1);

namespace Drupal\app_tag\Llms;

use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_platform\Llms\LlmsRenderEvent;
use Drupal\app_tag\Entity\TagBundle;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class TagPageLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function onLlmsRender(LlmsRenderEvent $event): void {
    if ($event->routeMatch->getRouteName() !== 'entity.taxonomy_term.canonical') {
      return;
    }

    $term = $event->routeMatch->getParameter('taxonomy_term');

    if (!$term instanceof TagBundle) {
      return;
    }

    $event->addCacheableDependency($term);

    $listCache = new CacheableMetadata();
    $listCache->addCacheTags(['node_list']);
    $event->addCacheableDependency($listCache);

    $items = $event->mainContent['#items'] ?? [];

    if (!\is_array($items) || $items === []) {
      return;
    }

    $lines = $this->buildArticleLines($event, \array_keys($items));
    $event->setMarkdown(\implode("\n", $lines));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsRenderEvent::class => 'onLlmsRender'];
  }

  /**
   * Builds markdown lines for articles referenced by the given node IDs.
   *
   * @return list<string>
   *   The formatted markdown lines.
   */
  private function buildArticleLines(LlmsRenderEvent $event, array $ids): array {
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($ids);
    $lines = [];

    foreach ($nodes as $node) {
      if (!$node instanceof ArticleBundle) {
        continue;
      }

      $event->addCacheableDependency($node);
      $url = $node->toUrl()->toString();
      $title = $node->getTitle() ?? '';
      $date = \date('Y-m-d', (int) $node->getCreatedTime());
      $lines[] = \sprintf('- [%s](%s) (%s)', $title, $url, $date);
    }

    return $lines;
  }

}
