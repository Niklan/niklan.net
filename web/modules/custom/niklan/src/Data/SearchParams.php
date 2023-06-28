<?php declare(strict_types = 1);

namespace Drupal\niklan\Data;

/**
 * Provides a DTO for search params.
 */
final class SearchParams {

  /**
   * Constructs a new SearchParams instance.
   *
   * @param string|null $keys
   *   The search keys.
   * @param int $limit
   *   The search results limit.
   * @param int $offset
   *   The offset for results.
   */
  public function __construct(
    protected ?string $keys,
    protected int $limit,
    protected int $offset = 0,
  ) {}

  /**
   * Gets the search keys.
   */
  public function getKeys(): ?string {
    return $this->keys;
  }

  /**
   * Gets search result limit.
   */
  public function getLimit(): int {
    return $this->limit;
  }

  /**
   * Gets search result offset.
   */
  public function getOffset(): int {
    return $this->offset;
  }

}
