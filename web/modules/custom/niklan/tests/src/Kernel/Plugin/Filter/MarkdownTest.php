<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Filter;

use Drupal\Core\Language\LanguageInterface;

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
    /** @var \Drupal\filter\Plugin\FilterInterface $filter */
    $filter = $this->filterManager->createInstance('niklan_markdown');

    $text = '**strong**';
    $result = $filter->process(
      $text,
      LanguageInterface::LANGCODE_NOT_SPECIFIED,
    );

    $this->assertStringContainsString(
      '<strong>strong</strong>',
      $result->getProcessedText(),
    );
  }

}
