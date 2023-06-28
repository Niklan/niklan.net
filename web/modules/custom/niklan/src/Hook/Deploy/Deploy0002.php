<?php declare(strict_types = 1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides initial External ID value for existing content.
 *
 * @see niklan_deploy_0002()
 */
final class Deploy0002 implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Constructs a new Deploy0002 instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Implements hook_deploy_HOOK().
   */
  public function __invoke(array &$sandbox): string {
    $this->prepareBatch($sandbox);

    if ($sandbox['total'] === 0) {
      $sandbox['#finished'] = 1;

      return 'No blog posts were found.';
    }

    $this->processBatch($sandbox);

    return (string) new FormattableMarkup('@current of @total blog posts are processed.', [
      '@current' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

  /**
   * Prepares variables for batch if they are not initialized.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function prepareBatch(array &$sandbox): void {
    if (isset($sandbox['total'])) {
      return;
    }

    $sandbox['total'] = $this->getQuery()->count()->execute();
    $sandbox['current'] = 0;
    $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
  }

  /**
   * Builds a default query for update.
   */
  protected function getQuery(): QueryInterface {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->sort('nid');
  }

  /**
   * Process a single batch.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function processBatch(array &$sandbox): void {
    $ids = $this
      ->getQuery()
      ->range($sandbox['current'], $sandbox['limit'])
      ->execute();

    $blog_posts = $this
      ->entityTypeManager
      ->getStorage('node')
      ->loadMultiple($ids);

    foreach ($blog_posts as $blog_post) {
      \assert($blog_post instanceof BlogEntryInterface);

      $blog_post->setExternalId($blog_post->id());
      $blog_post->save();

      $sandbox['current']++;
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];
  }

}
