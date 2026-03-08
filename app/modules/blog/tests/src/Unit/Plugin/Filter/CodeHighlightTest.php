<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter;

use Drupal\app_blog\Plugin\Filter\CodeHighlight;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CodeHighlight::class)]
final class CodeHighlightTest extends UnitTestCase {

  private CodeHighlight $filter;

  public function testLibraryAttachedWhenPrePresent(): void {
    $result = $this->filter->process('<p>Text</p><pre><code>code</code></pre>', 'en');

    $attachments = $result->getAttachments();
    self::assertContains('app_blog/hljs', $attachments['library']);
  }

  public function testLibraryNotAttachedWithoutPre(): void {
    $result = $this->filter->process('<p>No code here</p>', 'en');

    self::assertEmpty($result->getAttachments());
  }

  public function testTextPassedThrough(): void {
    $text = '<p>Hello <strong>world</strong></p>';
    $result = $this->filter->process($text, 'en');

    self::assertSame($text, $result->getProcessedText());
  }

  public function testCaseInsensitivePreDetection(): void {
    $result = $this->filter->process('<PRE>code</PRE>', 'en');

    $attachments = $result->getAttachments();
    self::assertContains('app_blog/hljs', $attachments['library']);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->filter = new CodeHighlight([], 'app_blog_code_highlight', ['provider' => 'app_blog']);
  }

}
