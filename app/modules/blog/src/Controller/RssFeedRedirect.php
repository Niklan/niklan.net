<?php

declare(strict_types=1);

namespace Drupal\app_blog\Controller;

use Drupal\Core\Cache\CacheableRedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Response;

final class RssFeedRedirect {

  public function __invoke(): CacheableRedirectResponse {
    $url = Url::fromRoute('app_blog.rss_feed')->setAbsolute()->toString();

    return new CacheableRedirectResponse((string) $url, Response::HTTP_MOVED_PERMANENTLY);
  }

}
