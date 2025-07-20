<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Utils;

use Drupal\external_content\Utils\PrioritizedList;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

#[CoversClass(PrioritizedList::class)]
#[Group('external_content')]
final class PrioritizedListTest extends UnitTestCase {

  public function testObject(): void {
    $instance = new PrioritizedList();

    $iterator_a = $instance->getIterator();
    self::assertEquals([], $iterator_a->getArrayCopy());

    $instance->add('a', -100);
    $instance->add('b', -100);
    $instance->add('c', 0);
    $instance->add('d', 100);

    $iterator_b = $instance->getIterator();
    self::assertEquals(['d', 'c', 'a', 'b'], $iterator_b->getArrayCopy());

    // Iterator A and B are different, because list is different.
    self::assertNotSame($iterator_a, $iterator_b);

    // Consequent calls without changing the list should return cached iterator.
    $iterator_c = $instance->getIterator();
    self::assertSame($iterator_b, $iterator_c);
    self::assertNotSame($iterator_a, $iterator_c);
  }

}
