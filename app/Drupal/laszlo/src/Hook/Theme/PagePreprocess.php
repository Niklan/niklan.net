<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

final readonly class PagePreprocess {

  public function __invoke(array &$variables): void {
    $this->prepareHeader($variables);
  }

  private function prepareHeader(array &$variables): void {
    $variables['header'] = [
      '#theme' => 'laszlo_page_header',
      '#cache' => [
        'keys' => ['laszlo', 'page', 'header'],
      ],
    ];
  }

}
