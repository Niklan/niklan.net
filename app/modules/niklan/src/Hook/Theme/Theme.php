<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'niklan_contact' => [
        'variables' => [
          'description' => NULL,
          'email' => NULL,
          'telegram' => NULL,
        ],
      ],
      'niklan_support' => [
        'variables' => [
          'description' => NULL,
          'donate_url' => NULL,
        ],
      ],
      'niklan_about' => [
        'variables' => [
          'photo_uri' => NULL,
          'heading' => NULL,
          'subtitle' => NULL,
          'summary' => NULL,
          'description' => NULL,
        ],
      ],
      'niklan_services' => [
        'variables' => [
          'description' => NULL,
          'hourly_rate' => NULL,
        ],
      ],
      'niklan_home' => [
        'variables' => [
          'sections' => [],
        ],
      ],
      'niklan_home_intro' => [
        'variables' => [
          'heading' => NULL,
          'description' => NULL,
        ],
      ],
      'niklan_home_cards' => [
        'variables' => [
          'cards' => [],
        ],
      ],
      'niklan_sitemap' => [
        'variables' => [
          'sitemap' => [],
        ],
      ],
    ];
  }

}
