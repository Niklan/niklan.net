<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter;

use Drupal\app_blog\Plugin\Filter\Markdown;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Markdown::class)]
final class MarkdownTest extends UnitTestCase {

  public function testBoldTextConvertedToStrong(): void {
    $filter = new Markdown([], Markdown::ID, ['provider' => 'app_blog']);

    $result = $filter->process('**strong**', 'en');

    self::assertStringContainsString('<strong>strong</strong>', $result->getProcessedText());
  }

}
