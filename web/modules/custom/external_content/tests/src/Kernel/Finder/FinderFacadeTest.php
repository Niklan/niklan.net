<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Contract\Finder\FinderFacadeInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Finder\FooFinder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides an external content finder test.
 *
 * @covers \Drupal\external_content\Finder\FinderFacade
 * @group external_content
 */
final class FinderFacadeTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  protected FinderFacadeInterface $finder;

  /**
   * {@selfdoc}
   */
  public function testEmptyFinder(): void {
    $environment = new Environment();
    $this->finder->setEnvironment($environment);
    $result = $this->finder->find();

    self::assertCount(0, $result);
  }

  /**
   * {@selfdoc}
   */
  public function testFooFinder(): void {
    $environment = new Environment();
    $environment->addFinder(FooFinder::class);
    $this->finder->setEnvironment($environment);
    $result = $this->finder->find();

    self::assertCount(1, $result);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->finder = $this
      ->container
      ->get(FinderFacadeInterface::class);
  }

}
