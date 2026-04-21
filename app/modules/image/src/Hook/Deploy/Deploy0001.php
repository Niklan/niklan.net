<?php

declare(strict_types=1);

namespace Drupal\app_image\Hook\Deploy;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Deploy0001 implements ContainerInjectionInterface {

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(StateInterface::class),
    );
  }

  public function __construct(
    private StateInterface $state,
  ) {}

  public function __invoke(): string {
    $this->state->delete('app_image.dynamic_image_style_key');
    return 'Deleted app_image.dynamic_image_style_key from state.';
  }

}
