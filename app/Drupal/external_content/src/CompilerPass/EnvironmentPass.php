<?php

declare(strict_types=1);

namespace Drupal\external_content\CompilerPass;

use Drupal\external_content\Exception\DuplicatedContainerDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class EnvironmentPass implements CompilerPassInterface {

  #[\Override]
  public function process(ContainerBuilder $container): void {
    $this->processEnvironments($container);
    $this->processComponent(
      container: $container,
      type: 'finder',
      service_tag: 'external_content.finder',
      parameter_name: 'external_content.finders',
    );
    $this->processComponent(
      container: $container,
      type: 'identifier',
      service_tag: 'external_content.identifier',
      parameter_name: 'external_content.identifiers',
    );
    $this->processComponent(
      container: $container,
      type: 'bundler',
      service_tag: 'external_content.bundler',
      parameter_name: 'external_content.bundlers',
    );
    $this->processComponent(
      container: $container,
      type: 'extension',
      service_tag: 'external_content.extension',
      parameter_name: 'external_content.extensions',
    );
    $this->processComponent(
      container: $container,
      type: 'converter',
      service_tag: 'external_content.converter',
      parameter_name: 'external_content.converters',
    );
    $this->processComponent(
      container: $container,
      type: 'loader',
      service_tag: 'external_content.loader',
      parameter_name: 'external_content.loaders',
    );
    $this->processComponent(
      container: $container,
      type: 'html_parser',
      service_tag: 'external_content.html_parser',
      parameter_name: 'external_content.html_parsers',
    );
    $this->processComponent(
      container: $container,
      type: 'serializer',
      service_tag: 'external_content.serializer',
      parameter_name: 'external_content.serializers',
    );
    $this->processComponent(
      container: $container,
      type: 'render_array_builder',
      service_tag: 'external_content.render_array_builder',
      parameter_name: 'external_content.render_array_builders',
    );
  }

  public function processEnvironments(ContainerBuilder $container): void {
    $environments = [];

    foreach ($container->findTaggedServiceIds('external_content.environment') as $service => $attributes) {
      $attributes = \reset($attributes);

      if (!\array_key_exists('id', $attributes) || !\array_key_exists('label', $attributes)) {
        continue;
      }

      $this->addEnvironment($service, $attributes, $environments);
      $this->setEnvironmentIdArgument(
        definition: $container->getDefinition($service),
        id: $attributes['id'],
      );
    }

    $container->setParameter('external_content.environments', $environments);
  }

  private function processComponent(ContainerBuilder $container, string $type, string $service_tag, string $parameter_name): void {
    $results = [];

    foreach ($container->findTaggedServiceIds($service_tag) as $service => $attributes) {
      $attributes = \reset($attributes);

      if (!\array_key_exists('id', $attributes)) {
        continue;
      }

      $this->addExtension($type, $service, $attributes, $results);
    }

    $container->setParameter($parameter_name, $results);
  }

  private function addEnvironment(string $service, array $attributes, array &$environments): void {
    if (\array_key_exists($attributes['id'], $environments)) {
      throw new DuplicatedContainerDefinition(
        type: 'environment',
        id: $attributes['id'],
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

  /**
   * Sets the environment id argument.
   *
   * If an environment definition doesn't set '$id' value, then do it
   * automatically from the tag 'id' value.
   */
  private function setEnvironmentIdArgument(Definition $definition, string $id): void {
    $arguments = $definition->getArguments();

    if (\array_key_exists(0, $arguments) || \array_key_exists('$id', $arguments)) {
      return;
    }

    $definition->setArgument('$id', $id);
  }

  /**
   * @param array{id: non-empty-string} $attributes
   */
  private function addExtension(string $type, string $service, array $attributes, array &$results): void {
    if (\array_key_exists($attributes['id'], $results)) {
      throw new DuplicatedContainerDefinition(
        type: $type,
        id: $attributes['id'],
        existing_service: $results[$attributes['id']]['service'],
        current_service: $service,
      );
    }

    $results[$attributes['id']] = [
      'service' => $service,
      'id' => $attributes['id'],
    ];
  }

}
