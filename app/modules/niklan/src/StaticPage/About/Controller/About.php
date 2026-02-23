<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\About\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\filter\Plugin\FilterInterface;
use Drupal\niklan\StaticPage\About\Repository\AboutSettings;
use Drupal\app_contract\Utils\MediaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class About implements ContainerInjectionInterface {

  public function __construct(
    private AboutSettings $settings,
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(AboutSettings::class),
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  private function preparePhotoUri(): ?string {
    $id = $this->settings->getPhotoMediaId();
    if (!\is_string($id)) {
      return NULL;
    }

    $media = $this->entityTypeManager->getStorage('media')->load($id);
    return MediaHelper::getFileUri($media);
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'niklan_about',
      '#photo_uri' => $this->preparePhotoUri(),
      '#heading' => $this->settings->getTitle(),
      '#subtitle' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getSubtitle(),
        '#format' => AboutSettings::TEXT_FORMAT,
        '#filter_types_to_skip' => [
          FilterInterface::TYPE_MARKUP_LANGUAGE,
        ],
      ],
      '#summary' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getSummary(),
        '#format' => AboutSettings::TEXT_FORMAT,
      ],
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => AboutSettings::TEXT_FORMAT,
      ],
    ];

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($this->settings);
    $cache->applyTo($build);

    return $build;
  }

}
