<?php

declare(strict_types=1);

namespace Drupal\app_image\PathProcessor;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\StreamWrapper\LocalStream;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/**
 * Rewrites dynamic image style URLs before core's PathProcessorImageStyles.
 *
 * Intercepts /files/styles/dynamic/... URLs and rewrites to the internal
 * route. The effects data and token are in query parameters, so no extraction
 * is needed — just a path rewrite to avoid core's image style routing.
 */
#[AutoconfigureTag('path_processor_inbound', ['priority' => 301])]
final readonly class DynamicImageStylePathProcessor implements InboundPathProcessorInterface {

  public function __construct(
    private StreamWrapperManagerInterface $streamWrapperManager,
  ) {}

  public function processInbound($path, Request $request): string {
    if (!$request->query->has('effects')) {
      return $path;
    }

    // Public files: /sites/default/files/styles/dynamic/...
    $wrapper = $this->streamWrapperManager->getViaScheme('public');
    Assert::isInstanceOf($wrapper, LocalStream::class);
    $directory_path = $wrapper->getDirectoryPath();
    $public_prefix = '/' . $directory_path . '/styles/dynamic/';
    if (\str_starts_with($path, $public_prefix)) {
      return '/' . $directory_path . '/styles/dynamic';
    }

    // Private files: /system/files/styles/dynamic/...
    // Set 'file' query param to block PathProcessorFiles (it skips processing
    // when 'file' is already present), then rewrite to the registered route.
    $private_prefix = '/system/files/styles/dynamic/';
    if (\str_starts_with($path, $private_prefix)) {
      $request->query->set('file', 'dynamic');
      return '/system/files/styles/dynamic';
    }

    return $path;
  }

}
