<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Llms;

use Drupal\app_platform\Llms\LlmsRenderEvent;
use Drupal\app_portfolio\Controller\PortfolioList;
use Drupal\app_portfolio\Node\PortfolioBundle;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class PortfolioListLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private PortfolioList $portfolioList,
    private TranslationInterface $translation,
  ) {}

  public function onLlmsRender(LlmsRenderEvent $event): void {
    if ($event->routeMatch->getRouteName() !== 'app_portfolio.portfolio_list') {
      return;
    }

    $listCache = new CacheableMetadata();
    $listCache->addCacheTags(['node_list:portfolio']);
    $event->addCacheableDependency($listCache);

    $lines = $this->buildProjectLines($event);
    $event->setMarkdown(\implode("\n", $lines));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsRenderEvent::class => 'onLlmsRender'];
  }

  /**
   * @return list<string>
   */
  private function buildProjectLines(LlmsRenderEvent $event): array {
    $lines = [];

    foreach ($this->portfolioList->load() as $node) {
      if (!$node instanceof PortfolioBundle) {
        continue;
      }

      $event->addCacheableDependency($node);
      $lines[] = $this->formatProject($node);
    }

    return $lines;
  }

  private function formatProject(PortfolioBundle $node): string {
    $url = $node->toUrl()->toString();
    $title = $node->getTitle() ?? '';
    $year = $node->getYearOfCompletion();

    $line = \sprintf('- [%s](%s)', $title, $url);

    if ($year !== NULL) {
      $line .= \sprintf(' (%s)', $year);
    }

    $categories = $node->getCategories();

    if ($categories !== []) {
      $names = \array_map(
        static fn ($term): string => (string) $term->label(),
        $categories,
      );
      $label = (string) $this->translation->translate('Categories');
      $line .= \sprintf(" \\\n  **%s**: %s", $label, \implode(', ', $names));
    }

    return $line;
  }

}
