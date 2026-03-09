<?php

declare(strict_types=1);

namespace Drupal\app_blog\Llms;

use Drupal\app_blog\Controller\BlogList;
use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_platform\Llms\LlmsRenderEvent;
use Drupal\Core\Cache\CacheableMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class BlogListLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private BlogList $blogList,
  ) {}

  public function onLlmsRender(LlmsRenderEvent $event): void {
    if ($event->routeMatch->getRouteName() !== 'app_blog.blog_list') {
      return;
    }

    $listCache = new CacheableMetadata();
    $listCache->addCacheTags(['node_list:blog_entry']);
    $event->addCacheableDependency($listCache);

    $lines = [];

    foreach ($this->blogList->load() as $node) {
      if (!($node instanceof ArticleBundle)) {
        continue;
      }

      $event->addCacheableDependency($node);
      $lines[] = $this->formatArticle($node);
    }

    $event->setMarkdown(\implode("\n", $lines));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsRenderEvent::class => 'onLlmsRender'];
  }

  private function formatArticle(ArticleBundle $node): string {
    $url = $node->toUrl()->toString();
    $title = $node->getTitle() ?? '';
    $date = \date('Y-m-d', (int) $node->getCreatedTime());
    $body = $node->get('body')->getString();

    $line = \sprintf('- [%s](%s) (%s)', $title, $url, $date);
    if ($body !== '') {
      $line .= \sprintf(" \\\n  %s", $body);
    }

    return $line;
  }

}
