<?php

declare(strict_types=1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Contract\Extension\ExtensionManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

final readonly class ContainerExtensionManager implements ExtensionManager {

  public const string TAG_NAME = 'external_content.extension';

  public function __construct(
    #[AutowireLocator(self::TAG_NAME, 'id')]
    private ContainerInterface $extensions,
  ) {}

  public function get(string $id): Extension {
    $extension = $this->extensions->get($id);
    \assert($extension instanceof Extension);
    return $extension;
  }

  public function has(string $id): bool {
    return $this->extensions->has($id);
  }

}
