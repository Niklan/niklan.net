<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Contract\ExternalContentFinderInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Finder\FooFinder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides an external content finder test.
 *
 * @covers \Drupal\external_content\Finder\ExternalContentFinder
 */
final class ExternalContentFinder extends ExternalContentTestBase {

  /**
   * The external content finder.
   */
  protected ExternalContentFinderInterface $finder;

  /**
   * Tests how it works without any finder.
   */
  public function testEmptyFinder(): void {
    $environment = new Environment(new Configuration());
    $this->finder->setEnvironment($environment);
    $result = $this->finder->find();

    self::assertCount(0, $result);
  }

  /**
   * Tests how it works with a foo finder.
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
