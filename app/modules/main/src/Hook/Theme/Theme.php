<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'app_main_contact' => [
        'variables' => [
          'description' => NULL,
          'email' => NULL,
          'telegram' => NULL,
        ],
      ],
      'app_main_support' => [
        'variables' => [
          'description' => NULL,
          'donate_url' => NULL,
        ],
      ],
      'app_main_about' => [
        'variables' => [
          'photo_uri' => NULL,
          'heading' => NULL,
          'subtitle' => NULL,
          'summary' => NULL,
          'description' => NULL,
        ],
      ],
      'app_main_services' => [
        'variables' => [
          'description' => NULL,
          'hourly_rate' => NULL,
        ],
      ],
      'app_main_home' => [
        'variables' => [
          'sections' => [],
        ],
      ],
      'app_main_home_intro' => [
        'variables' => [
          'heading' => NULL,
          'description' => NULL,
        ],
      ],
      'app_main_home_cards' => [
        'variables' => [
          'cards' => [],
        ],
      ],
      'app_main_sitemap' => [
        'variables' => [
          'sitemap' => [],
        ],
      ],
    ];
  }

}
