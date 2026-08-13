<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager\EventSubscriber;

use Drupal\Core\Pager\PagerManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Returns a 404 for pager pages that cannot exist.
 *
 * Two independent checks, since a single event cannot cover both cases:
 * - Negative or non-numeric "page" values (e.g. "-100", "abc") are obviously
 *   invalid and are rejected on KernelEvents::REQUEST, before the controller
 *   (and pager) even run.
 * - Numeric values beyond the last page (e.g. "999" on a 5-page listing)
 *   can only be judged on KernelEvents::RESPONSE, once the pager has counted
 *   the actual total.
 *
 * Without this, such requests get a misleading HTTP 200 (an empty or
 * duplicate result page), which search engines may index as thin content.
 *
 * @ingroup seo_pager
 */
final readonly class PagerNotFound implements EventSubscriberInterface {

  public function __construct(
    private PagerManagerInterface $pagerManager,
  ) {}

  public function onKernelRequest(RequestEvent $event): void {
    $request = $event->getRequest();
    $path = $request->getPathInfo();

    // Drupal's exception handling renders the 404 page via an internal
    // sub-request that carries over the original (invalid) query string.
    // Without this check, that sub-request would trip this same listener
    // and throw again, recursing until memory is exhausted.
    if (!$event->isMainRequest() || \stristr($path, '/admin') || \stristr($path, '/sitemap.xml')) {
      return;
    }

    $page = $request->query->get('page');

    if ($page === NULL || \preg_match('/^\d+$/', (string) $page) === 1) {
      return;
    }

    throw new NotFoundHttpException();
  }

  public function onKernelResponse(ResponseEvent $event): void {
    if (!$event->isMainRequest() || $event->getResponse()->getStatusCode() !== 200) {
      return;
    }

    $page = $event->getRequest()->query->get('page');

    if ($page === NULL || !$this->isOutOfRange((int) $page)) {
      return;
    }

    throw new NotFoundHttpException();
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onKernelRequest', 40],
      KernelEvents::RESPONSE => ['onKernelResponse'],
    ];
  }

  private function isOutOfRange(int $page): bool {
    $pager = $this->pagerManager->getPager();

    if ($pager === NULL || $pager->getTotalPages() < 1) {
      return FALSE;
    }

    return $page >= $pager->getTotalPages();
  }

}
