<?php

declare(strict_types=1);

namespace Drupal\app_image\DynamicImageStyle;

/**
 * Immutable builder for dynamic image style derivatives.
 *
 * Used in two modes:
 * - PHP API (without URI): chain effects, then call buildUrl($uri)
 * - Twig filters (with URI): chain effects, {{ }} renders URL via __toString()
 *
 * @code
 * // PHP — URI at the end:
 * $url = $dynamicImageStyle->effect('image_scale', ['width' => 300])->buildUrl($uri);
 *
 * // Twig — URI at the start:
 * {{ uri|image_scale(300)|image_convert('avif') }}
 * @endcode
 */
final readonly class DynamicImageStyleBuilder implements \Stringable {

  private const string DEFAULT_FORMAT = 'webp';

  /**
   * @param list<array{0: string, 1: array<string, mixed>}> $effects
   */
  public function __construct(
    private DynamicImageStyle $dynamicImageStyle,
    private array $effects = [],
    private ?string $uri = NULL,
  ) {}

  #[\Override]
  public function __toString(): string {
    if ($this->uri === NULL) {
      return '';
    }
    return $this->dynamicImageStyle->buildUrl($this->uri, $this->resolveEffects());
  }

  public function effect(string $id, array $data = []): self {
    return new self($this->dynamicImageStyle, [...$this->effects, [$id, $data]], $this->uri);
  }

  public function buildUrl(string $uri): string {
    return $this->dynamicImageStyle->buildUrl($uri, $this->resolveEffects());
  }

  public function buildUri(string $uri): string {
    return $this->dynamicImageStyle->buildUri($uri, $this->resolveEffects());
  }

  public function createDerivative(string $uri): bool {
    return $this->dynamicImageStyle->createDerivative($uri, $this->resolveEffects());
  }

  public function getUri(): ?string {
    return $this->uri;
  }

  /**
   * @return list<array{0: string, 1: array<string, mixed>}>
   */
  public function getEffects(): array {
    return $this->effects;
  }

  /**
   * @return list<array{0: string, 1: array<string, mixed>}>
   */
  private function resolveEffects(): array {
    if (\array_any($this->effects, static fn (array $effect): bool => $effect[0] === 'image_convert')) {
      return $this->effects;
    }
    return [...$this->effects, ['image_convert', ['extension' => self::DEFAULT_FORMAT]]];
  }

}
