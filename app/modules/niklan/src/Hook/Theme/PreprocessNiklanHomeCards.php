<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\media\MediaInterface;
use Drupal\niklan\StaticPage\Home\Repository\HomeSettings;
use Drupal\niklan\Utils\MediaHelper;

#[Hook('preprocess_niklan_home_cards')]
final readonly class PreprocessNiklanHomeCards {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function __invoke(array &$variables): void {
    $media_storage = $this->entityTypeManager->getStorage('media');
    foreach ($variables['cards'] as &$card) {

      $card['description'] = [
        '#type' => 'processed_text',
        '#text' => $card['description'],
        '#format' => HomeSettings::TEXT_FORMAT,
      ];

      $media = $media_storage->load($card['media_id']);
      if (!$media instanceof MediaInterface) {
        continue;
      }

      $card['background_uri'] = MediaHelper::getFileUri($media);
    }
  }

}
