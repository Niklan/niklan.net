<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Plugin\Filter;

use Drupal\Core\Language\LanguageInterface;

/**
 * Provides a test for markdown filter.
 *
 * @coversDefaultClass \Drupal\niklan\Plugin\Filter\PrismJsHighlighter
 */
final class PrismJsHighlighterTest extends FilterTestBase {

  /**
   * Tests that filter works as expected.
   */
  public function testFilter(): void {
    /** @var \Drupal\filter\Plugin\FilterInterface $filter */
    $filter = $this->filterManager->createInstance('niklan_prismjs');
    $expected_library = 'niklan/code-highlight';

    $text = '**strong**';
    $result = $filter->process($text, LanguageInterface::LANGCODE_NOT_SPECIFIED);
    $this->assertArrayNotHasKey('library', $result->getAttachments());

    $text = '<pre><code><?php echo "Hello, World!";</code></pre>';
    $result = $filter->process($text, LanguageInterface::LANGCODE_NOT_SPECIFIED);
    $this->assertContains($expected_library, $result->getAttachments()['library']);
  }

}
