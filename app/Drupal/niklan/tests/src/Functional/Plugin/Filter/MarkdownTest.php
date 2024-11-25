<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Functional\Plugin\Filter;

use Drupal\Core\Language\LanguageInterface;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Provides a test for markdown filter.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Filter\Markdown
 */
final class MarkdownTest extends FilterTestBase {

  /**
   * Tests that filter works as expected.
   */
  public function testFilter(): void {
    $filter = $this->filterManager->createInstance('niklan_markdown');
    \assert($filter instanceof FilterInterface);

    $text = '**strong**';
    $result = $filter->process(
      $text,
      LanguageInterface::LANGCODE_NOT_SPECIFIED,
    );

    self::assertStringContainsString(
      '<strong>strong</strong>',
      $result->getProcessedText(),
    );
  }

}
