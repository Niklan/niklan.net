<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class FirstPageRedirect implements EventSubscriberInterface {

  public function pageRedirect(RequestEvent $event): void {
    $request = $event->getRequest();
    if (!$event->isMainRequest() || PaginationPathPolicy::isExcluded($request->getPathInfo())) {
      return;
    }

    $query = $request->query->all();
    if (!\in_array($query['page'] ?? NULL, ['0', '1'], strict: TRUE)) {
      return;
    }

    $query = \array_diff_key($query, ['page' => TRUE]);
    $url = $request->getBaseUrl() . $request->getPathInfo();
    if ($query !== []) {
      $url .= '?' . \http_build_query($query, encoding_type: \PHP_QUERY_RFC3986);
    }
    $event->setResponse(new RedirectResponse($url, 301));
  }

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [
        // Higher priority than RedirectRequestSubscriber (33) to skip
        // unnecessary redirect processing on duplicate pager URLs.
        ['pageRedirect', 40],
      ],
    ];
  }

}
