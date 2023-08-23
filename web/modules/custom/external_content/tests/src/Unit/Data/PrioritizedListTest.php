<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\PrioritizedList;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a prioritized list test.
 *
 * @covers \Drupal\external_content\Data\PrioritizedList
 * @group external_content
 */
final class PrioritizedListTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $instance = new PrioritizedList();

    self::assertEquals([], $instance->getIterator()->getArrayCopy());

    $instance->add('a', -100);
    $instance->add('b', -100);
    $instance->add('c', 0);
    $instance->add('d', 100);

    self::assertEquals(
      ['d', 'c', 'a', 'b'],
      $instance->getIterator()->getArrayCopy(),
    );
  }

}
