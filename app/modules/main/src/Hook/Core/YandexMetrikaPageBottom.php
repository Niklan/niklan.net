<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Core;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Site\Settings;

#[Hook('page_bottom')]
final readonly class YandexMetrikaPageBottom {

  public function __invoke(array &$page_bottom): void {
    $metrika_id = Settings::get('app_yandex_metrika_id');
    if (!\is_string($metrika_id) || $metrika_id === '') {
      return;
    }

    $page_bottom['app_main_yandex_metrika'] = [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        'src' => 'https://mc.yandex.ru/watch/' . \rawurlencode($metrika_id),
        'style' => 'position:absolute; left:-9999px;',
        'alt' => '',
      ],
    ];
  }

}
