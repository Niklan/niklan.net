<?php declare(strict_types = 1);

namespace Drupal\external_content\Identifier;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\IdentifiedSourceCollection;
use Drupal\external_content\Data\SourceCollection;

/**
 * {@selfdoc}
 */
final class IdentifierManager implements IdentifierManagerInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function identify(SourceCollection $source_collection): IdentifiedSourceCollection {
    $identified_sources = new IdentifiedSourceCollection();

    foreach ($source_collection->items() as $source) {
      \assert($source instanceof SourceInterface);
      $this->identifySource($source, $identified_sources);
    }

    return $identified_sources;
  }

  /**
   * {@selfdoc}
   */
  private function identifySource(SourceInterface $source, IdentifiedSourceCollection $identified_sources): void {
    foreach ($this->environment->getIdentifiers() as $identifier) {
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
