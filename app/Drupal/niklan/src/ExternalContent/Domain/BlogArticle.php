<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Domain;

use Drupal\niklan\ExternalContent\Exception\PrimaryTranslationNotFoundException;

/**
 * @implements \IteratorAggregate<int, \Drupal\niklan\ExternalContent\Domain\BlogArticleTranslation>
 */
final class BlogArticle implements \IteratorAggregate, \Countable {

  /**
   * @var list<\Drupal\niklan\ExternalContent\Domain\BlogArticleTranslation>
   */
  private array $translations = [];

  /**
   * @param non-empty-string $id
   * @param non-empty-string $created
   * @param non-empty-string $updated
   * @param array{}|list<non-empty-string> $tags
   */
  public function __construct(
    public string $id,
    public string $created,
    public string $updated,
    public array $tags,
  ) {}

  public function addTranslation(BlogArticleTranslation $translation): void {
    if ($translation->isPrimary && $this->hasPrimaryTranslation()) {
      throw new \InvalidArgumentException('Only one primary translation allowed');
    }
    $this->translations[] = $translation;
  }

  /**
   * @return \ArrayIterator<int, \Drupal\niklan\ExternalContent\Domain\BlogArticleTranslation>
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->translations);
  }

  public function count(): int {
    return \count($this->translations);
  }

  public function hasPrimaryTranslation(): bool {
    foreach ($this->translations as $translation) {
      if (!$translation->isPrimary) {
        continue;
      }

      return TRUE;
    }

    return FALSE;
  }

  public function getPrimaryTranslation(): BlogArticleTranslation {
    foreach ($this->translations as $translation) {
      if ($translation->isPrimary) {
        return $translation;
      }
    }

    throw new PrimaryTranslationNotFoundException();
  }

  /**
   * @return array{}|list<\Drupal\niklan\ExternalContent\Domain\BlogArticleTranslation>
   */
  public function getTranslations(): array {
    return $this->translations;
  }

}
