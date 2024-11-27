<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Data;

/**
 * @todo Remove methods and make properties public readonly.
 */
final class EntitySearchResult {

  public function __construct(
    protected string $entityTypeId,
    protected int|string $entityId,
    protected string $language,
  ) {}

  public function getEntityTypeId(): string {
    return $this->entityTypeId;
  }

  public function getEntityId(): int|string {
    return $this->entityId;
  }

  public function getLanguage(): string {
    return $this->language;
  }

}
