<?php

declare(strict_types=1);

namespace Drupal\external_content\Identifier;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\IdentifiedSourceCollection;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class IdentifierManager implements IdentifierManagerInterface {

  public function __construct(
    private ContainerInterface $container,
    private array $identifiers = [],
  ) {}

  #[\Override]
  public function identify(SourceCollection $source_collection, EnvironmentInterface $environment): IdentifiedSourceCollection {
    $identified_sources = new IdentifiedSourceCollection();

    foreach ($source_collection->items() as $source) {
      \assert($source instanceof SourceInterface);
      $this->identifySource($source, $identified_sources, $environment);
    }

    return $identified_sources;
  }

  #[\Override]
  public function get(string $identifier_id): IdentifierInterface {
    if (!$this->has($identifier_id)) {
      throw new MissingContainerDefinitionException(
        type: 'identifier',
        id: $identifier_id,
      );
    }

    $service = $this->identifiers[$identifier_id]['service'];

    return $this->container->get($service);
  }

  #[\Override]
  public function has(string $identifier_id): bool {
    return \array_key_exists($identifier_id, $this->identifiers);
  }

  #[\Override]
  public function list(): array {
    return $this->identifiers;
  }

  private function identifySource(SourceInterface $source, IdentifiedSourceCollection $identified_sources, EnvironmentInterface $environment): void {
    foreach ($environment->getIdentifiers() as $identifier) {
      \assert($identifier instanceof IdentifierInterface);
      $identifier_result = $identifier->identify($source);

      if ($identifier_result->isNotIdentified()) {
        continue;
      }

      $identified_sources->add($identifier_result->result());

      return;
    }
  }

}
