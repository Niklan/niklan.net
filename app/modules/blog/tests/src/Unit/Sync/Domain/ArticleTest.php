<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Domain;

use Drupal\app_blog\Sync\Domain\Article;
use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Exception\PrimaryTranslationNotFoundException;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Article::class)]
final class ArticleTest extends UnitTestCase {

  public function testEmptyArticleHasNoTranslations(): void {
    $article = $this->createArticle();

    self::assertCount(0, $article);
    self::assertSame([], $article->getTranslations());
    self::assertFalse($article->hasPrimaryTranslation());
  }

  public function testAddTranslation(): void {
    $article = $this->createArticle();
    $translation = $this->createTranslation(language: 'ru');

    $article->addTranslation($translation);

    self::assertCount(1, $article);
    self::assertSame([$translation], $article->getTranslations());
  }

  public function testAddPrimaryTranslation(): void {
    $article = $this->createArticle();
    $primary = $this->createTranslation(language: 'ru', is_primary: TRUE);

    $article->addTranslation($primary);

    self::assertTrue($article->hasPrimaryTranslation());
    self::assertSame($primary, $article->getPrimaryTranslation());
  }

  public function testGetPrimaryTranslationThrowsWhenMissing(): void {
    $article = $this->createArticle();
    $article->addTranslation($this->createTranslation(language: 'ru'));

    $this->expectException(PrimaryTranslationNotFoundException::class);
    $article->getPrimaryTranslation();
  }

  public function testDuplicatePrimaryTranslationThrows(): void {
    $article = $this->createArticle();
    $article->addTranslation($this->createTranslation(language: 'ru', is_primary: TRUE));

    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Only one primary translation allowed');
    $article->addTranslation($this->createTranslation(language: 'en', is_primary: TRUE));
  }

  public function testMultipleNonPrimaryTranslations(): void {
    $article = $this->createArticle();
    $article->addTranslation($this->createTranslation(language: 'ru'));
    $article->addTranslation($this->createTranslation(language: 'en'));
    $article->addTranslation($this->createTranslation(language: 'de'));

    self::assertCount(3, $article);
    self::assertFalse($article->hasPrimaryTranslation());
  }

  public function testIterableOverTranslations(): void {
    $article = $this->createArticle();
    $ru = $this->createTranslation(language: 'ru');
    $en = $this->createTranslation(language: 'en');
    $article->addTranslation($ru);
    $article->addTranslation($en);

    $iterated = [];
    foreach ($article as $translation) {
      $iterated[] = $translation;
    }

    self::assertSame([$ru, $en], $iterated);
  }

  private function createArticle(): Article {
    return new Article(
      id: 'test-article',
      created: '2026-01-01T00:00:00',
      updated: '2026-03-01T00:00:00',
      tags: ['php', 'drupal'],
      directory: '/content/blog/test',
    );
  }

  private function createTranslation(string $language, bool $is_primary = FALSE): ArticleTranslation {
    return new ArticleTranslation(
      sourcePath: "$language/index.md",
      language: $language,
      title: "Title $language",
      description: "Description $language",
      posterPath: 'poster.png',
      contentDirectory: '/content/blog/test',
      isPrimary: $is_primary,
    );
  }

}
