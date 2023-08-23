<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\DependencyInjection;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\DependencyInjection\ClassA;
use Drupal\external_content_test\DependencyInjection\EnvironmentAwareClassA;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for environment aware class resolver.
 *
 * @covers \Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolver
 * @group external_content
 */
final class EnvironmentAwareClassResolverTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * {@selfdoc}
   */
  private ClassResolverInterface $classResolver;

  /**
   * {@selfdoc}
   */
  private EnvironmentAwareClassResolverInterface $environmentAwareClassResolver;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->classResolver = $this->container->get('class_resolver');
    $this->environmentAwareClassResolver = $this
      ->container
      ->get(EnvironmentAwareClassResolverInterface::class);
  }

  /**
   * {@selfdoc}
   */
  public function testFallback(): void {
    $environment = new Environment(new Configuration());

    $instance_a = $this
      ->classResolver
      ->getInstanceFromDefinition(ClassA::class);
    $instance_b = $this
      ->environmentAwareClassResolver
      ->getInstance(ClassA::class, ClassA::class, $environment);

    self::assertEquals($instance_a, $instance_b);
  }

  /**
   * {@selfdoc}
   */
  public function testEnvironmentInjection(): void {
    $environment = new Environment(new Configuration());

    $instance = $this->environmentAwareClassResolver->getInstance(
      EnvironmentAwareClassA::class,
      EnvironmentAwareClassA::class,
      $environment,
    );

    self::assertEquals($environment, $instance->getEnvironment());
  }

  /**
   * {@selfdoc}
   */
  public function testWrongInstance(): void {
    $environment = new Environment(new Configuration());

    self::expectException(\InvalidArgumentException::class);

    $this->environmentAwareClassResolver->getInstance(
      EnvironmentAwareClassA::class,
      ClassA::class,
      $environment,
    );
  }

  /**
   * {@selfdoc}
   */
  public function testReuseInstance(): void {
    $environment = new Environment(new Configuration());

    $instance_a = $this->environmentAwareClassResolver->getInstance(
      ClassA::class,
      ClassA::class,
      $environment,
    );
    $instance_b = $this->environmentAwareClassResolver->getInstance(
      ClassA::class,
      ClassA::class,
      $environment,
    );

    self::assertSame($instance_a, $instance_b);
  }

}
