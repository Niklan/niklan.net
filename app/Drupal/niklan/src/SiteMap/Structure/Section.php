<?php

declare(strict_types=1);

namespace Drupal\niklan\SiteMap\Structure;

use Drupal\Component\Assertion\Inspector;
use Drupal\Core\Link;

final class Section extends Element {

  public function __construct(
    public readonly \Stringable $heading,
  ) {}

  public function add(Link $link): void {
    $this->collection[] = $link;
  }

  public function setLinks(array $links): void {
    \assert(Inspector::assertAllObjects($links, Link::class));
    $this->collection = $links;
  }

  #[\Override]
  public function toArray(): array {
    return [
      'heading' => $this->heading,
      'links' => \array_map(static fn (Link $link): array => [
        'text' => $link->getText(),
        'url' => $link->getUrl(),
      ], $this->collection),
    ];
  }

}
