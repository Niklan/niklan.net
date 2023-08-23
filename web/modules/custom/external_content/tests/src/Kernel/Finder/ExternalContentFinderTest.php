<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Contract\Finder\ExternalContentFinderInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Finder\FooFinder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides an external content finder test.
 *
 * @covers \Drupal\external_content\Finder\ExternalContentFinder
 * @group external_content
 */
final class ExternalContentFinderTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  protected ExternalContentFinderInterface $finder;

  /**
   * {@selfdoc}
   */
  public function testEmptyFinder(): void {
    $environment = new Environment(new Configuration());
    $this->finder->setEnvironment($environment);
    $result = $this->finder->find();

    self::assertCount(0, $result);
  }

  /**
   * {@selfdoc}
   */
  public function testFooFinder(): void {
    $environment = new Environment(new Configuration());
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
      ->get(ExternalContentFinderInterface::class);
  }

}
