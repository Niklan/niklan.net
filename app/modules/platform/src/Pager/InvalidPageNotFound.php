<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Render\HtmlResponse;
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
    $response = $event->getResponse();
    if (!$event->isMainRequest() || !$response instanceof HtmlResponse || $response->getStatusCode() !== 200) {
      return;
    }

    if ($this->pagerManager->findPage() === 0 || PaginationPathPolicy::isExcluded($event->getRequest()->getPathInfo())) {
      return;
    }

    if (\in_array('pager:rendered', $response->getCacheableMetadata()->getCacheTags(), strict: TRUE)) {
      return;
    }

    throw new NotFoundHttpException();
  }

  public static function getSubscribedEvents(): array {
    return [
      // Lower than FirstPageRedirect (40) so that "0" and "1" are redirected
      // first, higher than the router and route normalizer redirects.
      KernelEvents::REQUEST => ['onRequest', 39],
      // Higher than DynamicPageCacheSubscriber (7), otherwise the 200 response
      // of a page without pager is already stored and later served as is.
      KernelEvents::RESPONSE => ['onResponse', 100],
    ];
  }

}
