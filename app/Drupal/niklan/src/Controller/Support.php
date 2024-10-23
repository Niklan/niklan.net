<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Contract\Repository\AboutSettings;
use Drupal\niklan\Contract\Repository\SupportSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Support implements ContainerInjectionInterface {

  public function __construct(
    private SupportSettings $settings,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(SupportSettings::class),
    );
  }

  public function __invoke(): array {
    return [
      '#theme' => 'niklan_support',
      '#body' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getBody(),
        '#format' => AboutSettings::TEXT_FORMAT,
      ],
      '#donate_url' => $this->settings->getDonateUrl(),
    ];
  }

}
