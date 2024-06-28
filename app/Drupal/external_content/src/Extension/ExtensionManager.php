<?php

declare(strict_types=1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\Extension\ExtensionManagerInterface;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 */
final readonly class ExtensionManager implements ExtensionManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ContainerInterface $container,
    private array $extensions = [],
  ) {}

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function get(string $extension_id): ExtensionInterface {
    if (!$this->has($extension_id)) {
      throw new MissingContainerDefinitionException(
        type: 'extension',
        id: $extension_id,
      );
    }

    $service = $this->extensions[$extension_id]['service'];

    return $this->container->get($service);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function has(string $extension_id): bool {
    return \array_key_exists($extension_id, $this->extensions);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function list(): array {
    return $this->extensions;
  }

}
