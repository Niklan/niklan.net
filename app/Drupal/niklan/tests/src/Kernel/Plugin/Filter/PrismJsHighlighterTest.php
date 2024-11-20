<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Filter;

use Drupal\Core\Language\LanguageInterface;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Provides a test for markdown filter.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Filter\CodeHighligt
 */
final class PrismJsHighlighterTest extends FilterTestBase {

  /**
   * Tests that filter works as expected.
   */
  public function testFilter(): void {
    $filter = $this->filterManager->createInstance('niklan_prismjs');
    \assert($filter instanceof FilterInterface);
    $expected_library = 'niklan/code-highlight';

    $text = '**strong**';
    $result = $filter->process(
      $text,
      LanguageInterface::LANGCODE_NOT_SPECIFIED,
    );
    self::assertArrayNotHasKey('library', $result->getAttachments());

    $text = '<pre><code><?php echo "Hello, World!";</code></pre>';
    $result = $filter->process(
      $text,
      LanguageInterface::LANGCODE_NOT_SPECIFIED,
    );
    self::assertContains(
      $expected_library,
      $result->getAttachments()['library'],
    );
  }

}
