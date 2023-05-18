<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\external_content\Plugin\ExternalContent\Source\SourcePlugin;
use Drupal\niklan\Repository\ContentSyncSettingsRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a content source for blog.
 *
 * @ExternalContentSource(
 *   id = "content",
 * )
 */
final class ContentSource extends SourcePlugin implements ContainerFactoryPluginInterface {

  /**
   * The content sync settings.
   */
  protected ContentSyncSettingsRepositoryInterface $contentSyncSettings;

  /**
   * Sets the content sync settings.
   *
   * @param \Drupal\niklan\Repository\ContentSyncSettingsRepositoryInterface $contentSyncSettings
   *   The content sync settings.
   */
  public function setContentSyncSettings(ContentSyncSettingsRepositoryInterface $contentSyncSettings): void {
    $this->contentSyncSettings = $contentSyncSettings;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->setContentSyncSettings(
      $container->get('niklan.repository.content_sync_settings'),
    );

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive(): bool {
    $working_dir = $this->contentSyncSettings->getWorkingDir();

    return $working_dir && \is_dir($working_dir);
  }

  /**
   * {@inheritdoc}
   */
  public function workingDir(): string {
    return $this->contentSyncSettings->getWorkingDir();
  }

}
