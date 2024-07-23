<?php

declare(strict_types=1);

namespace Drupal\external_content;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\external_content\CompilerPass\EnvironmentPass;

final class ExternalContentServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $container->addCompilerPass(new EnvironmentPass());
  }

}
