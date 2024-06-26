<?php declare(strict_types = 1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Remove paragraphs.
 *
 * @see niklan_deploy_0004()
 */
final readonly class Deploy0004 implements ContainerInjectionInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  /**
   * {@selfdoc}
   */
  public function __invoke(array &$sandbox): string {
    if (!isset($sandbox['total'])) {
      $sandbox['total'] = $this->getQuery()->count()->execute();
      $sandbox['current'] = 0;
      $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
    }

    if (!$sandbox['total']) {
      $sandbox['#finished'] = 1;

      return 'No paragraphs to delete.';
    }

    foreach ($this->paragraphs($sandbox) as $paragraph) {
      $paragraph->delete();
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];

    return (string) new FormattableMarkup('@count/@total paragraphs were removed.', [
      '@count' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('paragraph');
  }

  /**
   * {@selfdoc}
   */
  private function getQuery(): QueryInterface {
    return $this
      ->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->sort('id');
  }

  /**
   * {@selfdoc}
   */
  private function paragraphs(array &$sandbox): \Generator {
    $ids = $this
      ->getQuery()
      ->range(0, $sandbox['limit'])
      ->execute();
    $sandbox['current'] += \count($ids);

    yield from $this->getStorage()->loadMultiple($ids);
  }

}
