<?php declare(strict_types = 1);

namespace Drupal\external_content\CompilerPass;

use Drupal\external_content\Environment\EnvironmentManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * {@selfdoc}
 */
final class ExternalContentEnvironmentPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function process(ContainerBuilder $container): void {
    $manager = $container->getDefinition(EnvironmentManager::class);
    $environments = [];

    foreach ($container->findTaggedServiceIds('external_content.environment') as $service_id => $attributes) {
      $attributes = \reset($attributes);

      if (!\array_key_exists('id', $attributes) || !\array_key_exists('label', $attributes)) {
        continue;
      }

      $environments[$attributes['id']] = [
        'service' => $service_id,
        'label' => $attributes['label'],
      ];
    }

    $manager->setArgument(1, $environments);
  }

}
