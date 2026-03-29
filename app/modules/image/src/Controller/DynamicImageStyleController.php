<?php

declare(strict_types=1);

namespace Drupal\app_image\Controller;

use Drupal\app_image\DynamicImageStyle\DynamicImageStyle;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\StreamWrapper\LocalStream;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Webmozart\Assert\Assert;

/**
 * Delivers dynamically generated image derivatives.
 *
 * The original URI is reconstructed from the URL path:
 *   /files/styles/dynamic/{hash}/{scheme}/{target}?effects=...&itok=...
 * The PathProcessor strips the file path before routing, but the original
 * request URI is preserved in the Request object.
 */
final readonly class DynamicImageStyleController {

  public function __construct(
    private DynamicImageStyle $dynamicImageStyle,
    private ImageFactory $imageFactory,
    private StreamWrapperManagerInterface $streamWrapperManager,
    private ModuleHandlerInterface $moduleHandler,
    #[Autowire(service: 'lock')]
    private LockBackendInterface $lock,
  ) {}

  public function __invoke(Request $request): Response {
    [$original_uri, $effects, $compressed] = $this->validateRequest($request);

    $is_public = StreamWrapperManager::getScheme($original_uri) !== 'private';
    $headers = $this->checkFileAccess($original_uri, $is_public);

    $derivative_uri = $this->dynamicImageStyle->buildUri($original_uri, $effects);
    if (!\file_exists($derivative_uri)) {
      $this->generateDerivative($original_uri, $effects, $compressed);
    }

    return $this->deliverFile($derivative_uri, $headers, $is_public);
  }

  /**
   * Validates request parameters, token, and source file existence.
   *
   * @return array{string, list<array{0: string, 1: array<string, mixed>}>, string}
   *   A tuple of [original_uri, effects, compressed].
   */
  private function validateRequest(Request $request): array {
    $compressed = $request->query->getString('effects');
    $itok = $request->query->getString('itok');
    if ($compressed === '' || $itok === '') {
      throw new NotFoundHttpException();
    }

    $original_uri = $this->extractUri($request, $compressed);
    if ($original_uri === NULL) {
      throw new NotFoundHttpException();
    }

    if (!\hash_equals($this->dynamicImageStyle->generateToken($compressed, $original_uri), $itok)) {
      throw new NotFoundHttpException();
    }

    if (!\file_exists($original_uri)) {
      throw new NotFoundHttpException();
    }

    return [$original_uri, $this->tryDecompressEffects($compressed), $compressed];
  }

  /**
   * @return list<array{0: string, 1: array<string, mixed>}>
   */
  private function tryDecompressEffects(string $compressed): array {
    try {
      return $this->dynamicImageStyle->decompressEffects($compressed);
    }
    catch (\JsonException | \TypeError) {
      throw new NotFoundHttpException();
    }
  }

  /**
   * Checks file download access for private files.
   *
   * @return array<string, string>
   *   Headers from hook_file_download.
   */
  private function checkFileAccess(string $uri, bool $is_public): array {
    if ($is_public) {
      return [];
    }

    $headers = $this->moduleHandler->invokeAll('file_download', [$uri]);
    if (\in_array(-1, $headers) || $headers === []) {
      throw new AccessDeniedHttpException();
    }

    return $headers;
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  private function generateDerivative(string $original_uri, array $effects, string $compressed): void {
    $lock_name = 'dynamic_image_style:' . Crypt::hashBase64($compressed . ':' . $original_uri);
    if (!$this->lock->acquire($lock_name)) {
      throw new ServiceUnavailableHttpException(
        retryAfter: 3,
        message: 'Image generation in progress. Try again shortly.',
      );
    }

    try {
      $success = $this->dynamicImageStyle->createDerivative($original_uri, $effects);
    }
    finally {
      $this->lock->release($lock_name);
    }

    if (!$success) {
      throw new NotFoundHttpException();
    }
  }

  /**
   * Extracts the original image URI from the request path.
   *
   * URL: /{base}/styles/dynamic/{hash}/{scheme}/{target}[.{converted_ext}]
   * Result: {scheme}://{target} (without appended extension)
   */
  private function extractUri(Request $request, string $compressed): ?string {
    $path = $request->getPathInfo();
    $prefix = $this->detectPrefix($path);
    if ($prefix === NULL) {
      return NULL;
    }

    // Parse: {hash}/{scheme}/{target}.
    $parts = \explode('/', \substr($path, \strlen($prefix)), 3);
    if (\count($parts) !== 3) {
      return NULL;
    }
    [, $scheme, $target] = $parts;

    $target = $this->stripDerivativeExtension($target, $compressed);

    return $scheme . '://' . $target;
  }

  /**
   * Strips appended derivative extension (e.g., photo.jpg.webp → photo.jpg).
   */
  private function stripDerivativeExtension(string $target, string $compressed): string {
    try {
      $effects = $this->dynamicImageStyle->decompressEffects($compressed);
      $image_style = $this->dynamicImageStyle->createImageStyle($effects);
      $current_extension = \pathinfo($target, \PATHINFO_EXTENSION);
      $original_extension = \pathinfo(\pathinfo($target, \PATHINFO_FILENAME), \PATHINFO_EXTENSION);

      if ($original_extension !== '' && $image_style->getDerivativeExtension($original_extension) === $current_extension) {
        return \substr($target, 0, -(\strlen($current_extension) + 1));
      }
    }
    catch (\JsonException | \TypeError) {
      // Invalid effects — return target as-is, validation will fail later.
    }

    return $target;
  }

  private function detectPrefix(string $path): ?string {
    $wrapper = $this->streamWrapperManager->getViaScheme('public');
    Assert::isInstanceOf($wrapper, LocalStream::class);
    $public_prefix = '/' . $wrapper->getDirectoryPath() . '/styles/dynamic/';
    if (\str_starts_with($path, $public_prefix)) {
      return $public_prefix;
    }

    $private_prefix = '/system/files/styles/dynamic/';
    if (\str_starts_with($path, $private_prefix)) {
      return $private_prefix;
    }

    return NULL;
  }

  private function deliverFile(string $derivative_uri, array $headers = [], bool $is_public = TRUE): BinaryFileResponse {
    $image = $this->imageFactory->get($derivative_uri);
    $uri = $this->streamWrapperManager->normalizeUri($derivative_uri);

    $headers += [
      'Content-Type' => $image->getMimeType(),
      'Content-Length' => $image->getFileSize(),
    ];

    return new BinaryFileResponse(
      file: $uri,
      status: Response::HTTP_OK,
      headers: $headers,
      public: $is_public,
    );
  }

}
