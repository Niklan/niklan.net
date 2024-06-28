<?php

declare(strict_types=1);

namespace Drupal\niklan\Data;

/**
 * Provides an object that represents a single search result item.
 */
final class EntitySearchResult {

  /**
   * Constructs a new EntitySearchResultItem instance.
   *
   * @param string $entityTypeId
   *   The entity type ID.
   * @param int|string $entityId
   *   The entity ID.
   * @param string $language
   *   The result language.
   */
  public function __construct(
    protected string $entityTypeId,
    protected int|string $entityId,
    protected string $language,
  ) {}

  /**
   * Gets the entity type ID.
   */
  public function getEntityTypeId(): string {
    return $this->entityTypeId;
  }

  /**
   * Gets the entity ID.
   */
  public function getEntityId(): int|string {
    return $this->entityId;
  }

  /**
   * Gets the result language.
   */
  public function getLanguage(): string {
    return $this->language;
  }

}
