<?php

declare(strict_types=1);

namespace Drupal\app_file;

use Drupal\app_contract\Contract\File\FileSynchronizer;
use Drupal\app_file\File\DatabaseFileSynchronizer;
use Drupal\app_file\Hook\Entity\BaseFieldInfo;
use Drupal\app_file\Hook\Entity\BundleInfoAlter;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;

final readonly class AppFileServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    // Logger channel.
    $container->setDefinition(
      id: 'logger.channel.app_file',
      definition: (new ChildDefinition('logger.channel_base'))->addArgument('app_file'),
    );

    // File synchronization.
    $autowire(DatabaseFileSynchronizer::class);
    $container->setAlias(FileSynchronizer::class, DatabaseFileSynchronizer::class);

    // Hooks.
    $autowire(BaseFieldInfo::class);
    $autowire(BundleInfoAlter::class);
  }

}
