<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

/**
 * Decides on which paths the pager improvements must not be applied.
 *
 * The path is the only thing known everywhere: inbound path processors run
 * inside the router, and the subscribers run before it, so a route is not
 * available yet.
 *
 * @ingroup seo_pager
 */
final class PaginationPathPolicy {

  public static function isExcluded(string $path): bool {
    $path = \strtolower(\rawurldecode($path)) . '/';

    return \str_contains($path, '/admin/') || \str_contains($path, '/sitemap.xml/');
  }

}
