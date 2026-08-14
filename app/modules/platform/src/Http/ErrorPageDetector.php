<?php

declare(strict_types=1);

namespace Drupal\app_platform\Http;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Detects Drupal's internal error-page subrequest (e.g. for a 404/403).
 *
 * DefaultExceptionHtmlSubscriber::makeSubrequest() clones the original
 * request — keeping its path, query and route-default attributes — and
 * only merges the error route's own attributes on top, without clearing the
 * old ones. Anything that reads process-wide singleton state (e.g. the
 * pager manager) or `<current>`-based URLs during that subrequest would
 * otherwise see stale context leaked from the page that errored.
 *
 * Drupal marks such subrequests with an `_exception_statuscode` GET
 * parameter (added to the query, not to request attributes — see
 * DefaultExceptionHtmlSubscriber::makeSubrequest()), which is the reliable
 * way to detect this.
 */
final readonly class ErrorPageDetector {

  public function __construct(
    private RequestStack $requestStack,
  ) {}

  public function isErrorPageSubrequest(): bool {
    return $this->requestStack->getCurrentRequest()?->query->has('_exception_statuscode') ?? FALSE;
  }

}
