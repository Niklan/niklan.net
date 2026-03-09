<?php

declare(strict_types=1);

namespace Drupal\app_tag\Llms;

use Drupal\app_contract\Contract\Tag\TagUsageStatistics;
use Drupal\app_platform\Llms\LlmsRenderEvent;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class TagListLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private TagUsageStatistics $statistics,
    private TranslationInterface $translation,
  ) {}

  public function onLlmsRender(LlmsRenderEvent $event): void {
    if ($event->routeMatch->getRouteName() !== 'app_tag.tag_list') {
      return;
    }

    $listCache = new CacheableMetadata();
    $listCache->addCacheTags(['taxonomy_term_list']);
    $event->addCacheableDependency($listCache);

    $usage = $this->statistics->usage();
    $ids = \array_keys($usage);

    if ($ids === []) {
      return;
    }

    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadMultiple($ids);
    $lines = [];

    foreach ($terms as $term) {
      $event->addCacheableDependency($term);
      $count = (int) ($usage[$term->id()]->count ?? 0);
      $lines[] = $this->formatTag($term, $count);
    }

    $event->setMarkdown(\implode("\n", $lines));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsRenderEvent::class => 'onLlmsRender'];
  }

  private function formatTag(TermInterface $term, int $count): string {
    $url = $term->toUrl()->toString();
    $label = (string) $this->translation->translate('@count publications', ['@count' => $count]);

    return \sprintf('- [%s](%s) (%s)', $term->label(), $url, $label);
  }

}
