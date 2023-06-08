<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\external_content\Contract\LoaderPluginInterface;
use Drupal\external_content\Contract\LoaderPluginManagerInterface;
use Drupal\external_content\Data\ExternalContent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a queue worker for content synchronization.
 *
 * @QueueWorker(
 *   id = "content_sync",
 *   title = @Translation("Content Synchronization"),
 * )
 *
 * @ingroup content_sync
 */
final class ContentSyncQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected LoaderPluginManagerInterface $loaderPluginManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get(LoaderPluginManagerInterface::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem(mixed $data): void {
    if (!$data instanceof ExternalContent) {
      return;
    }

    $content_loader = $this->loaderPluginManager->createInstance('content');

    if (!$content_loader instanceof LoaderPluginInterface) {
      return;
    }

    $content_loader->load($data);
  }

}
