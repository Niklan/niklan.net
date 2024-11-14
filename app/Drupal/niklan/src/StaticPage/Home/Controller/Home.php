<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Home\Controller;

final readonly class Home {

  public function __invoke(): array {
    return [
      '#theme' => 'niklan_home',
    ];
  }

}