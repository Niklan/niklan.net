<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter;

use Drupal\app_blog\Plugin\Filter\FootnoteFilter;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FootnoteFilter::class)]
final class FootnoteFilterTest extends UnitTestCase {

  private FootnoteFilter $filter;

  public function testLibraryAttachedWhenFootnotesPresent(): void {
    $html = '<p>Text<sup class="footnote-ref"><a href="#fn1">1</a></sup></p>';
    $result = $this->filter->process($html, 'en');

    $attachments = $result->getAttachments();
    self::assertContains('app_blog/footnote.tooltip', $attachments['library']);
  }

  public function testLibraryNotAttachedWithoutFootnotes(): void {
    $result = $this->filter->process('<p>No footnotes</p>', 'en');

    self::assertEmpty($result->getAttachments());
  }

  public function testTextPassedThrough(): void {
    $text = '<p>Some text</p>';
    $result = $this->filter->process($text, 'en');

    self::assertSame($text, $result->getProcessedText());
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->filter = new FootnoteFilter([], 'app_blog_footnote', ['provider' => 'app_blog']);
  }

}
