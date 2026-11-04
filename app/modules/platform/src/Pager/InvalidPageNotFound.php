<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class InvalidPageNotFound implements EventSubscriberInterface {

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

  public static function getSubscribedEvents(): array {
    return [
      // Lower than FirstPageRedirect (40) so that "0" and "1" are redirected
      // first, higher than the router and route normalizer redirects.
      KernelEvents::REQUEST => ['onRequest', 39],
    ];
  }

}
