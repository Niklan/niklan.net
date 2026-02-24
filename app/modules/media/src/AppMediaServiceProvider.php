<?php

declare(strict_types=1);

namespace Drupal\app_media;

use Drupal\app_contract\Contract\Media\MediaRepository;
use Drupal\app_contract\Contract\Media\MediaSynchronizer;
use Drupal\app_media\Media\DatabaseMediaRepository;
use Drupal\app_media\Media\DatabaseMediaSynchronizer;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;

final readonly class AppMediaServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    // Logger channel.
    $container->setDefinition(
      id: 'logger.channel.app_media',
      definition: (new ChildDefinition('logger.channel_base'))->addArgument('app_media'),
    );

    // Media synchronization and repository.
    $autowire(DatabaseMediaSynchronizer::class);
    $container->setAlias(MediaSynchronizer::class, DatabaseMediaSynchronizer::class);

    $autowire(DatabaseMediaRepository::class);
    $container->setAlias(MediaRepository::class, DatabaseMediaRepository::class);
  }

}
