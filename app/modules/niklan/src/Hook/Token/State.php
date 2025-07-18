<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Token;

use Drupal\Core\Render\BubbleableMetadata;

final class State {

  /**
   * @see hook_tokens()
   */
  public function __construct(
    protected array $replacements,
    protected array $tokens,
    protected array $data,
    protected array $options,
    protected BubbleableMetadata $bubbleableMetadata,
  ) {}

  public function getReplacements(): array {
    return $this->replacements;
  }

  public function setReplacements(array $replacements): self {
    $this->replacements = $replacements;

    return $this;
  }

  public function setReplacement(string $original, string $replacement): self {
    $this->replacements[$original] = $replacement;

    return $this;
  }

  public function getTokens(): array {
    return $this->tokens;
  }

  public function getTokenOriginal(string $token): ?string {
    return $this->tokens[$token] ?? NULL;
  }

  public function replaceCallback(string $token, callable $callback): void {
    $original = $this->getTokenOriginal($token);

    if (!$original) {
      return;
    }

    $callback($original, $this);
  }

  public function getData(): array {
    return $this->data;
  }

  public function getOptions(): array {
    return $this->options;
  }

  public function getCacheableMetadata(): BubbleableMetadata {
    return $this->bubbleableMetadata;
  }

}
