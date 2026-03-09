<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms;

use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class LlmsResponseAlterEvent extends Event {

  public function __construct(
    private string $markdown,
    public readonly Request $request,
    public readonly RouteMatchInterface $routeMatch,
  ) {}

  public function getMarkdown(): string {
    return $this->markdown;
  }

  public function setMarkdown(string $markdown): void {
    $this->markdown = $markdown;
  }

  public function prepend(string $content): void {
    $this->markdown = $content . $this->markdown;
  }

  public function append(string $content): void {
    $this->markdown .= $content;
  }

}
