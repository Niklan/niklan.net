<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Domain;

use Drupal\app_blog\Sync\Exception\PrimaryTranslationNotFoundException;

/**
 * @implements \IteratorAggregate<int, \Drupal\app_blog\Sync\Domain\ArticleTranslation>
 */
final class Article implements \IteratorAggregate, \Countable {

  /**
   * @var list<\Drupal\app_blog\Sync\Domain\ArticleTranslation>
   */
  private array $translations = [];

  /**
   * @param string $id
   * @param string $created
   * @param string $updated
   * @param array{}|list<string> $tags
   * @param string $directory
   */
  public function __construct(
    public string $id,
    public string $created,
    public string $updated,
    public array $tags,
    public string $directory,
  ) {}

  public function addTranslation(ArticleTranslation $translation): void {
    if ($translation->isPrimary && $this->hasPrimaryTranslation()) {
      throw new \InvalidArgumentException('Only one primary translation allowed');
    }
    $this->translations[] = $translation;
  }

  /**
   * @return \ArrayIterator<int, \Drupal\app_blog\Sync\Domain\ArticleTranslation>
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->translations);
  }

  public function count(): int {
    return \count($this->translations);
  }

  public function hasPrimaryTranslation(): bool {
    return \array_any($this->translations, static fn (ArticleTranslation $t): bool => $t->isPrimary);
  }

  public function getPrimaryTranslation(): ArticleTranslation {
    return \array_find($this->translations, static fn (ArticleTranslation $t): bool => $t->isPrimary)
      ?? throw new PrimaryTranslationNotFoundException();
  }

  /**
   * @return array{}|list<\Drupal\app_blog\Sync\Domain\ArticleTranslation>
   */
  public function getTranslations(): array {
    return $this->translations;
  }

}
