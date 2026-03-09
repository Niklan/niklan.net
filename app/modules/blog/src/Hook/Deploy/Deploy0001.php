<?php

declare(strict_types=1);

namespace Drupal\app_blog\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Removes numeric /blog/{nid} aliases, creates 301 redirects to node/{nid}.
 */
final class Deploy0001 implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  protected function prepareBatch(array &$sandbox): void {
    if (isset($sandbox['total'])) {
      return;
    }

    $sandbox['total'] = (int) $this->getNodeQuery()->count()->execute();
    $sandbox['current'] = 0;
    $sandbox['created'] = 0;
    $sandbox['skipped'] = 0;
    $sandbox['aliases_removed'] = 0;
    $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
  }

  protected function processBatch(array &$sandbox): void {
    /** @var array<int|string> $nids */
    $nids = $this
      ->getNodeQuery()
      ->range($sandbox['current'], $sandbox['limit'])
      ->execute();

    $redirect_storage = $this->entityTypeManager->getStorage('redirect');

    foreach ($nids as $nid) {
      $sandbox['aliases_removed'] += $this->removeNumericAliases((int) $nid);

      $source_path = "blog/{$nid}";

      $existing = $redirect_storage
        ->getQuery()
        ->accessCheck(FALSE)
        ->condition('redirect_source.path', $source_path)
        ->range(0, 1)
        ->execute();

      if ($existing !== []) {
        $sandbox['skipped']++;
      }
      else {
        $redirect = $redirect_storage->create([
          'redirect_source' => $source_path,
          'redirect_redirect' => "internal:/node/{$nid}",
          'status_code' => 301,
        ]);
        $redirect_storage->save($redirect);
        $sandbox['created']++;
      }

      $sandbox['current']++;
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];
  }

  protected function removeNumericAliases(int $nid): int {
    $alias_storage = $this->entityTypeManager->getStorage('path_alias');

    /** @var array<int|string> $alias_ids */
    $alias_ids = $alias_storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('alias', "/blog/{$nid}")
      ->execute();

    if ($alias_ids === []) {
      return 0;
    }

    $aliases = $alias_storage->loadMultiple($alias_ids);
    $alias_storage->delete($aliases);

    return \count($aliases);
  }

  protected function getNodeQuery(): QueryInterface {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->sort('nid');
  }

  public function __invoke(array &$sandbox): string {
    $this->prepareBatch($sandbox);

    if ($sandbox['total'] === 0) {
      $sandbox['#finished'] = 1;
      return 'No blog entries found.';
    }

    $this->processBatch($sandbox);

    return (string) new FormattableMarkup('@current of @total processed (@created redirects, @skipped skipped, @aliases aliases removed).', [
      '@current' => $sandbox['current'],
      '@total' => $sandbox['total'],
      '@created' => $sandbox['created'],
      '@skipped' => $sandbox['skipped'],
      '@aliases' => $sandbox['aliases_removed'],
    ]);
  }

}
