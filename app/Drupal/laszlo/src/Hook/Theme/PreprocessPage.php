<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

final readonly class PreprocessPage {

  private function prepareHeader(array &$variables): void {
    $variables['header'] = [
      '#theme' => 'laszlo_page_header',
      '#cache' => [
        'keys' => ['laszlo', 'page', 'header'],
      ],
    ];
  }

  private function prepareFooter(array &$variables): void {
    $variables['footer'] = [
      '#theme' => 'laszlo_page_footer',
      '#cache' => [
        'keys' => ['laszlo', 'page', 'footer'],
      ],
    ];
  }

  public function __invoke(array &$variables): void {
    $this->prepareHeader($variables);
    $this->prepareFooter($variables);
  }

}
