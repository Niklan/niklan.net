<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Drupal\app_blog\Node\ArticleBundle;
use Drupal\pathauto\PathautoState;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Deploy0006 implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  private function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('node');
  }

  private function getQuery(): QueryInterface {
    return $this
      ->getStorage()
      ->getQuery()
      ->condition('type', 'blog_entry')
      ->accessCheck(FALSE)
      ->sort('nid');
  }

  private function article(array &$sandbox): \Generator {
    $ids = $this
      ->getQuery()
      ->range($sandbox['current'], $sandbox['limit'])
      ->execute();
    \assert(\is_array($ids));
    $sandbox['current'] += \count($ids);

    yield from $this->getStorage()->loadMultiple($ids);
  }

  private function process(ArticleBundle $article): void {
    $article->set('path', ['pathauto' => PathautoState::CREATE]);
    $article->save();
  }

  public function __invoke(array &$sandbox): string {
    if (!isset($sandbox['total'])) {
      $sandbox['total'] = $this->getQuery()->count()->execute();
      $sandbox['current'] = 0;
      $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
    }

    if (!$sandbox['total']) {
      $sandbox['#finished'] = 1;

      return 'No blog articles to process.';
    }

    foreach ($this->article($sandbox) as $article) {
      \assert($article instanceof ArticleBundle);
      $this->process($article);
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];

    return (string) new FormattableMarkup('@count/@total blog articles were processed.', [
      '@count' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

}
