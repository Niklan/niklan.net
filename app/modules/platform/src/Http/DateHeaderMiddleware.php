<?php

declare(strict_types=1);

namespace Drupal\app_platform\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Fixes the frozen Date header produced by Drupal's page cache.
 *
 * Problem:
 *   Symfony sets the Date header when a Response object is first created.
 *   Drupal's page cache serializes the entire Response — including its
 *   headers — and replays it verbatim on cache hits. The Date header is
 *   frozen at the moment the cache entry was written, not at the moment
 *   the response is actually sent to the client.
 *
 *   Browsers use Date to calculate the apparent age of a response:
 *
 *     apparent_age = now - Date
 *     is_fresh = apparent_age < max-age
 *
 *   With max-age=900 (15 min) and a cache entry written 20 minutes ago,
 *   the browser receives apparent_age=1200 > max-age=900 and treats the
 *   response as stale — even though it just arrived. A stale response
 *   cannot be stored in the prefetch cache, so <link rel="prefetch">
 *   requests made by ForesightJS are discarded and the next navigation
 *   still hits the server.
 *
 * Why CDNs do not have this problem:
 *   CDNs such as Cloudflare replace Date with the current time before
 *   forwarding the response. Without a CDN the frozen date reaches the
 *   browser unchanged.
 *
 * RFC note:
 *   RFC 7231 §7.1.1.2 defines Date as "the date and time at which the
 *   message was originated". This is intentionally ambiguous for cached
 *   responses: it could mean when the content was first generated (Drupal
 *   freezes it here) or when the HTTP message is transmitted to the client
 *   (what this middleware sets it to). RFC 7234 §5.1 offers the formally
 *   correct solution — adding an Age header so the browser knows how old
 *   the stored response is — but with max-age=900 and a cache entry that
 *   lives indefinitely (invalidated by cache tags, not by TTL), Age would
 *   still make the response appear stale. Resetting Date to the current
 *   transmission time is the pragmatic fix, and matches what CDNs such as
 *   Cloudflare do in practice.
 *
 * Solution:
 *   This middleware wraps the kernel stack at priority 201 (just above
 *   PageCache at 200) and overwrites Date with the current time on every
 *   response. The browser always sees apparent_age ≈ 0 and the freshness
 *   window starts from the moment of receipt.
 *
 *   Last-Modified and ETag are not touched: Last-Modified still reflects
 *   the actual content-change time and ETag still enables conditional
 *   revalidation once the freshness window expires.
 *
 * @see https://httpwg.org/specs/rfc7231.html#header.date
 */
final readonly class DateHeaderMiddleware implements HttpKernelInterface {

  public function __construct(private HttpKernelInterface $httpKernel) {}

  #[\Override]
  public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = TRUE): Response {
    $response = $this->httpKernel->handle($request, $type, $catch);
    $response->setDate(new \DateTime());
    return $response;
  }

}
