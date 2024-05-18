<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Finder\FooFinder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides an external content finder test.
 *
 * @covers \Drupal\external_content\Finder\FinderManager
 * @group external_content
 */
final class FinderManagerTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testEmptyFinder(): void {
    $environment = new Environment('test');
    $result = $this->getManager()->find($environment);

    self::assertCount(0, $result->items());
  }

  /**
   * {@selfdoc}
   */
  private function getManager(): FinderManagerInterface {
    return $this->container->get(FinderManagerInterface::class);
  }

  /**
   * {@selfdoc}
   */
  public function testFooFinder(): void {
    $environment = new Environment('test');
    $environment->addFinder(new FooFinder());
    $result = $this->getManager()->find($environment);

    self::assertCount(1, $result->items());
  }

}
