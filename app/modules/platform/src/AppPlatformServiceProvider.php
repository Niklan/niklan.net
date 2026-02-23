<?php

declare(strict_types=1);

namespace Drupal\app_platform;

use Drupal\app_contract\Contract\Console\Git;
use Drupal\app_contract\Contract\File\FileSynchronizer;
use Drupal\app_contract\Contract\Media\MediaRepository;
use Drupal\app_contract\Contract\Media\MediaSynchronizer;
use Drupal\app_contract\Contract\SiteMap\SiteMapBuilder;
use Drupal\app_platform\Console\Process\ProcessGit;
use Drupal\app_platform\Console\Process\ProcessTerminal;
use Drupal\app_platform\File\DatabaseFileSynchronizer;
use Drupal\app_platform\Hook\Asset\CacheBustingQuerySetting;
use Drupal\app_platform\Hook\Entity\BaseFieldInfo;
use Drupal\app_platform\Hook\Entity\BundleInfoAlter;
use Drupal\app_platform\Hook\Theme\LibraryInfoAlter;
use Drupal\app_platform\LanguageAwareStore\EventSubscriber\LanguageAwareSettingsRoutes;
use Drupal\app_platform\LanguageAwareStore\Factory\DatabaseLanguageAwareFactory;
use Drupal\app_platform\LanguageAwareStore\Factory\ServiceContainerLanguageAwareFactory;
use Drupal\app_platform\Markup\Twig\Extension\ImageDimensions;
use Drupal\app_platform\Media\DatabaseMediaRepository;
use Drupal\app_platform\Media\DatabaseMediaSynchronizer;
use Drupal\app_platform\Pager\Controller\PagerAwareTitleResolver;
use Drupal\app_platform\Pager\EventSubscriber\PagerRedirect;
use Drupal\app_platform\Pager\PathProcessor\PagerPathProcessor;
use Drupal\app_platform\SiteMap\Structure\SiteMapManager;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;

final readonly class AppPlatformServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    // Console.
    $container
      ->autowire('app_platform.process.terminal', ProcessTerminal::class)
      ->setPublic(TRUE);
    $autowire(ProcessGit::class);
    $container->setAlias(Git::class, ProcessGit::class);

    // LanguageAwareStore.
    $autowire(ServiceContainerLanguageAwareFactory::class);
    $container->setAlias('keyvalue.language_aware', ServiceContainerLanguageAwareFactory::class);
    $autowire(DatabaseLanguageAwareFactory::class);
    $container->setAlias('keyvalue.language_aware.database', DatabaseLanguageAwareFactory::class);

    $autowire(LanguageAwareSettingsRoutes::class)
      ->addTag('event_subscriber');

    // Markup.
    $autowire(ImageDimensions::class)
      ->addTag('twig.extension');

    // SiteMap.
    $autowire(SiteMapManager::class);
    $container->setAlias(SiteMapBuilder::class, SiteMapManager::class);

    // Pager.
    $autowire(PagerRedirect::class)
      ->addTag('event_subscriber');
    $autowire(PagerPathProcessor::class)
      ->addTag('path_processor_inbound', ['priority' => 1000])
      ->addTag('path_processor_outbound', ['priority' => -1000]);
    $autowire(PagerAwareTitleResolver::class);

    // Logger channels.
    $container->setDefinition(
      id: 'logger.channel.app_platform',
      definition: (new ChildDefinition('logger.channel_base'))->addArgument('app_platform'),
    );

    // File & Media.
    $autowire(DatabaseFileSynchronizer::class);
    $container->setAlias(FileSynchronizer::class, DatabaseFileSynchronizer::class);

    $autowire(DatabaseMediaSynchronizer::class);
    $container->setAlias(MediaSynchronizer::class, DatabaseMediaSynchronizer::class);

    $autowire(DatabaseMediaRepository::class);
    $container->setAlias(MediaRepository::class, DatabaseMediaRepository::class);

    // Hooks.
    $autowire(CacheBustingQuerySetting::class);
    $autowire(LibraryInfoAlter::class);
    $autowire(BaseFieldInfo::class);
    $autowire(BundleInfoAlter::class);

  }

}
