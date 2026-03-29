<?php

declare(strict_types=1);

namespace Drupal\app_main\CompilerPass;

use Drupal\app_contract\Stub\Stub;
use Drupal\app_image\DynamicImageStyle\DynamicImageStyle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class StubsCompilerPass implements CompilerPassInterface {

  #[\Override]
  public function process(ContainerBuilder $container): void {
    $this->stubService($container, DynamicImageStyle::class);
  }

  /**
   * @param class-string $class
   */
  private function stubService(ContainerBuilder $container, string $class): void {
    if ($container->hasDefinition($class) || $container->hasAlias($class)) {
      return;
    }

    if (!$container->hasDefinition(Stub::class)) {
      $container->autowire(Stub::class, Stub::class);
    }

    $container->setAlias($class, Stub::class);
  }

}
