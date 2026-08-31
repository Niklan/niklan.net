<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager\PathProcessor;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ingroup seo_pager
 */
final class PagerPathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  #[\Override]
  public function processInbound($path, Request $request): string {
    if (!$this->isApplicable($path)) {
      return $path;
    }

    // Guard against double processing: PathBasedBreadcrumbBuilder calls
    // processInbound() again on the same request, which would decrement
    // the already-converted internal page number a second time.
    if ($request->query->has('page') && !$request->attributes->has('_pager_processed')) {
      $page_external = (int) $request->query->get('page');
      $page_internal = $page_external ? $page_external - 1 : 0;
      $request->query->set('page', $page_internal);
      $request->attributes->set('_pager_processed', value: TRUE);
    }

    return $path;
  }

  #[\Override]
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if (!$this->isApplicable($path, $options)) {
      return $path;
    }

    // A non-numeric "page" (e.g. leaked from an invalid request while
    // building an error page's "return to" URL) has no valid outbound
    // representation, so it is dropped rather than causing a TypeError
    // below.
    if (!\is_numeric($options['query']['page'])) {
      unset($options['query']['page']);

      return $path;
    }

    if (\in_array($options['query']['page'], [0, '0'])) {
      unset($options['query']['page']);
    }
    elseif ($options['query']['page'] > 0) {
      $page_internal = $options['query']['page'];
      $page_external = $page_internal + 1;
      $options['query']['page'] = $page_external;
    }

    return $path;
  }

  private function isApplicable(string $path, ?array $options = NULL): bool {
    if (\stristr($path, '/admin') || \stristr($path, '/sitemap.xml')) {
      return FALSE;
    }

    return !$options || isset($options['query']['page']);
  }

}
