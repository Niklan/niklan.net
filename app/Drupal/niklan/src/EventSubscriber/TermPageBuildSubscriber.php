<?php

declare(strict_types=1);

namespace Drupal\niklan\EventSubscriber;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\niklan\Controller\Tag;
use Drupal\taxonomy_custom_controller\Event\TaxonomyCustomControllerEvents;
use Drupal\taxonomy_custom_controller\Event\TermPageBuildEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TermPageBuildSubscriber implements EventSubscriberInterface {

  public function __construct(
    protected ClassResolverInterface $classResolver,
  ) {}

  public function onTermPageBuild(TermPageBuildEvent $event): void {
    $taxonomy_term = $event->getTaxonomyTerm();

    if ($taxonomy_term->bundle() !== 'tags') {
      return;
    }

    $controller = $this
      ->classResolver
      ->getInstanceFromDefinition(Tag::class);
    \assert($controller instanceof Tag);
    $event->setBuildArray($controller($taxonomy_term));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      TaxonomyCustomControllerEvents::PAGE_BUILD => 'onTermPageBuild',
    ];
  }

}
