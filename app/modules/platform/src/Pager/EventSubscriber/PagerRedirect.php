<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Provides pager redirect.
 *
 * The pager number in the query is adjusted to match the page number, so page
 * 1 has a query of ?page=1. However, /foo?page=1 is the same as /foo. To
 * remove these duplicates, this subscriber redirects to a variant without a
 * query.
 *
 * @ingroup seo_page
 */
final readonly class PagerRedirect implements EventSubscriberInterface {

  public function onKernelRequest(RequestEvent $event): void {
    $request = $event->getRequest();
    $path = $request->getPathInfo();

    if (\stristr($path, '/admin') || \stristr($path, '/sitemap.xml')) {
      return;
    }

    $page = (string) $request->query->get('page', '');
    $is_first_pages = \in_array($request->query->get('page'), ['0', '1'], TRUE);
    // Negative pagers can be entered manually, Drupal simply fails on them.
    // This code will also fix that behavior for an edge cases.
    $is_negative_page = \str_starts_with($page, '-');

    if (!$is_first_pages && !$is_negative_page) {
      return;
    }

    $query = $request->query->all();
    unset($query['page']);
    $url = Url::createFromRequest($request);
    $url->setOption('query', $query);
    $redirect = new TrustedRedirectResponse($url->setAbsolute()->toString(), 301);
    $event->setResponse($redirect);
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      // It should have a bigger priority than
      // \Drupal\redirect\EventSubscriber\RedirectRequestSubscriber in order to
      // avoid unnecessary logic.
      KernelEvents::REQUEST => ['onKernelRequest', 40],
    ];
  }

}
