<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter\Stub;

use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;

/**
 * Stub renderer that sets #attached on rendered build arrays.
 *
 * Prophecy cannot modify by-reference parameters, so this stub is needed
 * to test that filters propagate #attached from rendered components.
 */
final class StubRenderer implements RendererInterface {

  private int $callCount = 0;

  /**
   * @param string $html
   *   HTML to return from renderInIsolation().
   * @param array<string, mixed>|null $attachments
   *   Attachments to set on every render call. If NULL, sequential
   *   attachments are generated as ['library' => ['test/lib-N']].
   */
  public function __construct(
    private readonly string $html,
    private readonly ?array $attachments = NULL,
  ) {}

  public function getCallCount(): int {
    return $this->callCount;
  }

  #[\Override]
  public function renderInIsolation(&$elements): Markup {
    $this->callCount++;
    $elements['#attached'] = $this->attachments ?? ['library' => ["test/lib-{$this->callCount}"]];

    return $this->markup($this->html);
  }

  #[\Override]
  public function renderRoot(&$elements): Markup {
    return $this->markup('');
  }

  /**
   * @param array $elements
   *   The render array.
   */
  #[\Override]
  public function renderPlain(&$elements): Markup {
    return $this->markup('');
  }

  #[\Override]
  public function renderPlaceholder($placeholder, array $elements): array {
    return $elements;
  }

  #[\Override]
  public function render(&$elements, $is_root_call = FALSE): Markup {
    return $this->markup('');
  }

  #[\Override]
  public function hasRenderContext(): bool {
    return FALSE;
  }

  #[\Override]
  public function executeInRenderContext(RenderContext $context, callable $callable): mixed {
    return $callable();
  }

  #[\Override]
  public function mergeBubbleableMetadata(array $a, array $b): array {
    return $a;
  }

  #[\Override]
  public function addCacheableDependency(array &$elements, $dependency): void {
    // Intentionally empty — not needed for filter tests.
  }

  private function markup(string $html): Markup {
    $result = Markup::create($html);
    \assert($result instanceof Markup);

    return $result;
  }

}
