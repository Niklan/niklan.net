<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Domain;

use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArticleTranslation::class)]
final class ArticleTranslationTest extends UnitTestCase {

  public function testConstructorSetsProperties(): void {
    $translation = new ArticleTranslation(
      sourcePath: 'en/index.md',
      language: 'en',
      title: 'Title',
      description: 'Description',
      posterPath: 'poster.png',
      contentDirectory: '/content/blog/article',
      isPrimary: TRUE,
    );

    self::assertSame('en/index.md', $translation->sourcePath);
    self::assertSame('en', $translation->language);
    self::assertSame('Title', $translation->title);
    self::assertSame('Description', $translation->description);
    self::assertSame('poster.png', $translation->posterPath);
    self::assertSame('/content/blog/article', $translation->contentDirectory);
    self::assertTrue($translation->isPrimary);
  }

  public function testIsPrimaryDefaultsFalse(): void {
    $translation = $this->createTranslation();

    self::assertFalse($translation->isPrimary);
  }

  public function testAttachmentsEmptyByDefault(): void {
    $translation = $this->createTranslation();

    self::assertSame([], $translation->getAttachments());
  }

  public function testAddAndGetAttachments(): void {
    $translation = $this->createTranslation();

    $translation->addAttachment(['src' => 'file.pdf', 'title' => 'PDF']);
    $translation->addAttachment(['src' => 'doc.zip', 'title' => 'Archive']);

    $attachments = $translation->getAttachments();
    self::assertCount(2, $attachments);
    self::assertSame('file.pdf', $attachments[0]['src']);
    self::assertSame('PDF', $attachments[0]['title']);
    self::assertSame('doc.zip', $attachments[1]['src']);
  }

  private function createTranslation(): ArticleTranslation {
    return new ArticleTranslation(
      sourcePath: 'index.md',
      language: 'ru',
      title: 'Test',
      description: 'Test',
      posterPath: 'poster.png',
      contentDirectory: '/tmp/test',
    );
  }

}
