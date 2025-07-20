<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Functional\Plugin\Filter;

use Drupal\Core\Language\LanguageInterface;
use Drupal\filter\Plugin\FilterInterface;
use Drupal\niklan\Plugin\Filter\Markdown;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Markdown::class)]
final class MarkdownTest extends FilterTestBase {

  /**
   * Tests that filter works as expected.
   */
  public function testFilter(): void {
    $filter = $this->filterManager->createInstance(Markdown::ID);
    \assert($filter instanceof FilterInterface);

    $text = '**strong**';
    $result = $filter->process($text, LanguageInterface::LANGCODE_NOT_SPECIFIED);

    self::assertStringContainsString('<strong>strong</strong>', $result->getProcessedText());
  }

}
