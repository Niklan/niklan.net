<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\media\MediaInterface;
use Drupal\niklan\StaticPage\Home\Repository\HomeSettings;
use Drupal\niklan\Utils\MediaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessNiklanHomeCards implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

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
