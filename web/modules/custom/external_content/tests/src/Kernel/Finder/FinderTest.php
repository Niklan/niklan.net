<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Finder\FooFinder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides an external content finder test.
 *
 * @covers \Drupal\external_content\Finder\FinderFacade
 * @group external_content
 */
final class FinderTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  private function getFinder(): FinderInterface {
    return $this->container->get(FinderInterface::class);
  }

  /**
   * {@selfdoc}
   */
  public function testEmptyFinder(): void {
    $environment = new Environment();
    $this->getFinder()->setEnvironment($environment);
    $result = $this->getFinder()->find();

    self::assertCount(0, $result);
  }

  /**
   * {@selfdoc}
   */
  public function testFooFinder(): void {
    $environment = new Environment();
    $environment->addFinder(new FooFinder());
    $this->getFinder()->setEnvironment($environment);
    $result = $this->getFinder()->find();

    self::assertCount(1, $result);
  }

}
