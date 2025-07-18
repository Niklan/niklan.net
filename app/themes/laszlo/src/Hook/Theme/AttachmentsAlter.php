<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class AttachmentsAlter implements ContainerInjectionInterface {

  public function __construct(
    private LanguageManagerInterface $languageManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(LanguageManagerInterface::class),
    );
  }

  private function preloadFont(array &$attachments): void {
    $fonts = [
      '/libraries/lora/v35/latin-italic.woff2',
      '/libraries/lora/v35/latin-normal.woff2',
    ];

    if ($this->languageManager->getCurrentLanguage()->getId() === 'ru') {
      $fonts[] = '/libraries/lora/v35/cyrillic-normal.woff2';
      $fonts[] = '/libraries/lora/v35/cyrillic-italic.woff2';
    }

    foreach ($fonts as $delta => $font) {
      $attachments['#attached']['html_head'][] = [
        [
          '#tag' => 'link',
          '#attributes' => [
            'rel' => 'preload',
            'as' => 'font',
            'type' => 'font/woff2',
            'crossorigin' => '',
            'href' => $font,
          ],
        ],
        'preload_font_' . $delta,
      ];
    }
  }

  public function __invoke(array &$attachments): void {
    $this->preloadFont($attachments);
  }

}
