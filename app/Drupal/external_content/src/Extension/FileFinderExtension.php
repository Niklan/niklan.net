<?php

declare(strict_types=1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Finder\FileFinder;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

/**
 * Provides a very basic extension with most useful settings.
 */
final readonly class FileFinderExtension implements ConfigurableExtensionInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private FileFinder $fileFinder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addFinder($this->fileFinder);
  }

  /**
   * {@inheritdoc}
   */
  public function configureSchema(ConfigurationBuilderInterface $builder): void {
    $builder->addSchema('file_finder', Expect::structure([
      'extensions' => Expect::arrayOf('string')->required(),
      'directories' => Expect::arrayOf('string')->required(),
    ]));
  }

}
