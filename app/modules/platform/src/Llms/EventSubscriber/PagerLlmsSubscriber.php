<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms\EventSubscriber;

use Drupal\app_platform\Llms\LlmsResponseAlterEvent;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class PagerLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private PagerManagerInterface $pagerManager,
    private TranslationInterface $translation,
  ) {}

  public function onAlter(LlmsResponseAlterEvent $event): void {
    $pager = $this->pagerManager->getPager();

    if ($pager === NULL || $pager->getTotalPages() <= 1) {
      return;
    }

    $event->getCacheableMetadata()->addCacheContexts(['url.query_args.pagers:0']);

    $current = $pager->getCurrentPage();
    $total = $pager->getTotalPages();
    $last = $total - 1;

    $heading = (string) $this->translation->translate('Navigation');
    $page_status = (string) $this->translation->translate('Page @current of @total', [
      '@current' => $current + 1,
      '@total' => $total,
    ]);

    $lines = [];
    $lines[] = "\n\n---\n";
    $lines[] = \sprintf("## %s\n", $heading);
    $lines[] = \sprintf("%s\n", $page_status);

    if ($current > 0) {
      $lines[] = \sprintf('- [%s](%s)', $this->translation->translate('First page'), $this->buildPageUrl(0));
      $lines[] = \sprintf('- [%s](%s)', $this->translation->translate('Previous page'), $this->buildPageUrl($current - 1));
    }

    if ($current < $last) {
      $lines[] = \sprintf('- [%s](%s)', $this->translation->translate('Next page'), $this->buildPageUrl($current + 1));
      $lines[] = \sprintf('- [%s](%s)', $this->translation->translate('Last page'), $this->buildPageUrl($last));
    }

    $event->append(\implode("\n", $lines));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsResponseAlterEvent::class => 'onAlter'];
  }

  private function buildPageUrl(int $page): string {
    $options = [];

    if ($page > 0) {
      $options['query']['page'] = $page;
    }

    return Url::fromRoute('<current>', options: $options)->toString();
  }

}
