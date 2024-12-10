<?php

declare(strict_types=1);

namespace Drupal\niklan\SiteMap\Structure;

final class Category extends Element {

  public function __construct(
    public readonly \Stringable $heading,
  ) {}

  public function add(Section $section): void {
    $this->collection[] = $section;
  }

  #[\Override]
  public function toArray(): array {
    return [
      'heading' => (string) $this->heading,
      'sections' => \array_map(static fn (Section $section) => $section->toArray(), $this->collection),
    ];
  }

}
