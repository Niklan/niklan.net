<?php

declare(strict_types=1);

namespace Drupal\app_image\DynamicImageStyle;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\PrivateKey;
use Drupal\Core\StreamWrapper\LocalStream;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\image\ImageStyleInterface;
use Webmozart\Assert\Assert;

/**
 * Service for generating dynamic image style derivatives.
 *
 * Instead of pre-configured image styles, this service builds derivative
 * URLs on the fly using existing ImageEffect plugins from core and contrib.
 *
 * Usage:
 * @code
 * $url = $dynamicImageStyle
 *   ->effect('image_scale', ['width' => 300, 'height' => 200])
 *   ->effect('image_convert', ['extension' => 'webp'])
 *   ->buildUrl('public://photos/sunset.jpg');
 * @endcode
 */
final readonly class DynamicImageStyle {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private StreamWrapperManagerInterface $streamWrapperManager,
    private PrivateKey $privateKey,
  ) {}

  public function effect(string $id, array $data = []): DynamicImageStyleBuilder {
    return new DynamicImageStyleBuilder($this)->effect($id, $data);
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  public function buildUrl(string $uri, array $effects): string {
    [$scheme, $target, $compressed, $hash] = $this->resolveDerivativePath($uri, $effects);
    $itok = $this->generateToken($compressed, $uri);
    $encoded_effects = \urlencode($compressed);
    $base_path = $this->getBaseUrlPath($scheme);
    return "/$base_path/styles/dynamic/$hash/$scheme/$target?effects=$encoded_effects&itok=$itok";
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  public function buildUri(string $uri, array $effects): string {
    [$scheme, $target, , $hash] = $this->resolveDerivativePath($uri, $effects);
    return "$scheme://styles/dynamic/$hash/$scheme/$target";
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  public function createDerivative(string $uri, array $effects): bool {
    $derivative_uri = $this->buildUri($uri, $effects);
    $image_style = $this->createImageStyle($effects);
    return $image_style->createDerivative($uri, $derivative_uri);
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  public function compressEffects(array $effects): string {
    $json = \json_encode($effects, flags: \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES);
    return UrlHelper::compressQueryParameter($json);
  }

  /**
   * @return list<array{0: string, 1: array<string, mixed>}>
   */
  public function decompressEffects(string $compressed): array {
    $json = UrlHelper::uncompressQueryParameter($compressed);
    /** @var list<array{0: string, 1: array<string, mixed>}> $effects */
    $effects = \json_decode($json, associative: TRUE, flags: \JSON_THROW_ON_ERROR);
    return $effects;
  }

  public function generateToken(string $compressed, string $uri): string {
    return \substr(Crypt::hmacBase64($compressed . ':' . $uri, $this->privateKey->get()), 0, 8);
  }

  private function hashEffects(string $compressed): string {
    return \substr(Crypt::hashBase64($compressed), 0, 8);
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   * @return array{string, string, string, string}
   */
  private function resolveDerivativePath(string $uri, array $effects): array {
    $scheme = StreamWrapperManager::getScheme($uri);
    $target = StreamWrapperManager::getTarget($uri);
    Assert::string($scheme);
    Assert::string($target);

    $image_style = $this->createImageStyle($effects);
    $original_extension = \pathinfo($target, \PATHINFO_EXTENSION);
    $derivative_extension = $image_style->getDerivativeExtension($original_extension);
    if ($original_extension !== $derivative_extension) {
      $target .= '.' . $derivative_extension;
    }

    $compressed = $this->compressEffects($effects);
    $hash = $this->hashEffects($compressed);

    return [$scheme, $target, $compressed, $hash];
  }

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  public function createImageStyle(array $effects): ImageStyleInterface {
    $storage = $this->entityTypeManager->getStorage('image_style');
    $image_style = $storage->create(['name' => 'dynamic']);
    Assert::isInstanceOf($image_style, ImageStyleInterface::class);

    foreach ($effects as [$id, $data]) {
      $image_style->addImageEffect(['id' => $id, 'data' => $data]);
    }

    return $image_style;
  }

  /**
   * Returns the base URL path for the given stream wrapper scheme.
   *
   * Public files: sites/default/files (served by nginx directly after first
   * generation).
   * Private files: system/files (always served through PHP with access checks).
   */
  private function getBaseUrlPath(string $scheme): string {
    if ($scheme === 'private') {
      return 'system/files';
    }
    $wrapper = $this->streamWrapperManager->getViaScheme($scheme);
    Assert::isInstanceOf($wrapper, LocalStream::class);
    return $wrapper->getDirectoryPath();
  }

}
