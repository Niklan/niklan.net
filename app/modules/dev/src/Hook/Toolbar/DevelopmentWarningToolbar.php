<?php

declare(strict_types=1);

namespace Drupal\app_dev\Hook\Toolbar;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Provides a development warning toolbar element.
 *
 * @ingroup toolbar
 */
#[Hook('toolbar')]
final class DevelopmentWarningToolbar {

  public function __construct(
    private readonly TranslationInterface $stringTranslation,
  ) {}

  /**
   * Implements hook_toolbar().
   */
  public function __invoke(): array {
    // Add a warning about using a dev site.
    $items['dev-site-warning'] = [
      '#weight' => 999,
    ];

    $label = $this->stringTranslation->translate('Development version');

    $items['dev-site-warning']['#type'] = 'toolbar_item';
    $items['dev-site-warning']['tab'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="toolbar-warning">{{ label }}</div>',
      '#context' => [
        'label' => $label,
      ],
      '#attached' => [
        'library' => ['app_dev/development-warning.toolbar'],
      ],
    ];

    return $items;
  }

}
