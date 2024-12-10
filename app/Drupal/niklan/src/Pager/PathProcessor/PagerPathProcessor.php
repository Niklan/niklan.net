<?php

declare(strict_types=1);

namespace Drupal\niklan\Pager\PathProcessor;

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
    if (\stristr($path, '/admin') || \stristr($path, '/sitemap.xml')) {
      return $path;
    }

    if ($request->query->has('page')) {
      $page_external = $request->query->get('page');
      $page_internal = $page_external ? $page_external - 1 : 0;
      $request->query->set('page', $page_internal);
    }

    return $path;
  }

  #[\Override]
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if (\stristr($path, '/admin') || \stristr($path, '/sitemap.xml')) {
      return $path;
    }

    if (isset($options['query']['page'])) {
      if (\in_array($options['query']['page'], [0, '0'])) {
        unset($options['query']['page']);
      }
      elseif ($options['query']['page'] > 0) {
        $page_internal = $options['query']['page'];
        $page_external = $page_internal + 1;
        $options['query']['page'] = $page_external;
      }
    }

    return $path;
  }

}
