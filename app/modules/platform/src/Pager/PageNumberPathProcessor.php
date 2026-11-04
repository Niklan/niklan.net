<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;

#[AutoconfigureTag('path_processor_inbound', ['priority' => 1000])]
#[AutoconfigureTag('path_processor_outbound', ['priority' => -1000])]
final readonly class PageNumberPathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  public function processInbound($path, Request $request): string {
    if (PaginationPathPolicy::isExcluded($path)) {
      return $path;
    }

    // Guard against double processing: PathBasedBreadcrumbBuilder calls
    // processInbound() again on the same request, which would decrement
    // the already-converted internal page number a second time.
    if ($request->query->has('page') && !$request->attributes->has('_pager_processed')) {
      $page_internal = \max(0, $request->query->getInt('page') - 1);
      $request->query->set('page', $page_internal);
      $request->attributes->set('_pager_processed', value: TRUE);
    }

    return $path;
  }

  /**
   * @param array{query?: array<string, mixed>} $options
   */
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if (PaginationPathPolicy::isExcluded($path)) {
      return $path;
    }

    $page = $options['query']['page'] ?? NULL;
    if (!\is_int($page) && !\is_string($page)) {
      return $path;
    }

    $page = (int) $page;
    if ($page === 0) {
      unset($options['query']['page']);
    }
    elseif ($page > 0) {
      $options['query']['page'] = $page + 1;
    }

    return $path;
  }

}
