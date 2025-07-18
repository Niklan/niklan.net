<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Deploy0009 implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  public function __invoke(array &$sandbox): string {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $ids = $storage->getQuery()->accessCheck(FALSE)->notExists('external_id')->execute();
    $tags = $storage->loadMultiple($ids);
    $storage->delete($tags);
    return 'All categories without External ID have been removed.';
  }

}
