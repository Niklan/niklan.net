<?php

declare(strict_types=1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Contract\Extension\ExtensionManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

final readonly class ContainerExtensionManager implements ExtensionManager {

  public function __construct(
    #[AutowireLocator('external_content.extension', 'id')]
    private ContainerInterface $extensions,
  ) {}

  public function get(string $id): Extension {
    return $this->extensions->get($id);
  }

  public function has(string $id): bool {
    return $this->extensions->has($id);
  }

}
