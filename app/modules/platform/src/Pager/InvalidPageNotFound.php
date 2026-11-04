<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

use Drupal\Core\Pager\PagerManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class InvalidPageNotFound implements EventSubscriberInterface {

  public function __construct(
    private PagerManagerInterface $pagerManager,
  ) {}

  public function onRequest(RequestEvent $event): void {
    $request = $event->getRequest();
    if (!$event->isMainRequest() || PaginationPathPolicy::isExcluded($request->getPathInfo())) {
      return;
    }

    $page = $request->query->all()['page'] ?? '0';
    if (!\is_string($page) || !\preg_match('/^(?:0|[1-9]\d{0,8})\z/', $page)) {
      // The 404 page is rendered by a subrequest that inherits the query, and
      // core pager cache contexts throw on a non-scalar "page" there.
      $request->query->remove('page');
      throw new NotFoundHttpException();
    }
  }

  public function onResponse(ResponseEvent $event): void {
    $is_successful = $event->getResponse()->getStatusCode() === 200;
    $is_excluded = PaginationPathPolicy::isExcluded($event->getRequest()->getPathInfo());
    if (!$event->isMainRequest() || !$is_successful || $is_excluded) {
      return;
    }

    $page = $this->pagerManager->findPage();
    if ($page === 0) {
      return;
    }

    // The pager is NULL when the controller did not run (Dynamic Page Cache
    // hit) as well, so its absence says nothing about the page being invalid.
    $pager = $this->pagerManager->getPager();
    if ($pager !== NULL && $page >= $pager->getTotalPages()) {
      throw new NotFoundHttpException();
    }
  }

  public static function getSubscribedEvents(): array {
    return [
      // Lower than FirstPageRedirect (40) so that "0" and "1" are redirected
      // first, higher than the router and route normalizer redirects.
      KernelEvents::REQUEST => ['onRequest', 39],
      // Higher than DynamicPageCacheSubscriber (7), otherwise the out-of-range
      // 200 response is already stored and later served without a 404.
      KernelEvents::RESPONSE => ['onResponse', 100],
    ];
  }

}
