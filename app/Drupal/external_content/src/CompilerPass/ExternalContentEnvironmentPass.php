<?php declare(strict_types = 1);

namespace Drupal\external_content\CompilerPass;

use Drupal\external_content\Exception\DuplicatedContainerEnvironmentDefinition;
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
    $environments = [];

    foreach ($container->findTaggedServiceIds('external_content.environment') as $service => $attributes) {
      $attributes = \reset($attributes);

      if (!\array_key_exists('id', $attributes) || !\array_key_exists('label', $attributes)) {
        continue;
      }

      $this->addEnvironment($service, $attributes, $environments);
    }

    $container->setParameter('external_content.environments', $environments);
  }

  /**
   * {@selfdoc}
   */
  private function addEnvironment(string $service, mixed $attributes, array &$environments): void {
    if (\array_key_exists($attributes['id'], $environments)) {
      throw new DuplicatedContainerEnvironmentDefinition(
        environment_id: $attributes['id'],
        existing_service: $environments[$attributes['id']]['service'],
        current_service: $service,
      );
    }

    $environments[$attributes['id']] = [
      'service' => $service,
      'id' => $attributes['id'],
      'label' => $attributes['label'],
    ];
  }

}
