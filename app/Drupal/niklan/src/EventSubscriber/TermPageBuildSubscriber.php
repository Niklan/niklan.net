<?php

declare(strict_types=1);

namespace Drupal\niklan\EventSubscriber;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\niklan\Controller\TagController;
use Drupal\niklan\Controller\TagControllerInterface;
use Drupal\taxonomy_custom_controller\Event\TaxonomyCustomControllerEvents;
use Drupal\taxonomy_custom_controller\Event\TermPageBuildEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides a subscriber for term page build.
 */
final class TermPageBuildSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a new TermPageBuildSubscriber object.
   *
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected ClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      TaxonomyCustomControllerEvents::PAGE_BUILD => 'onTermPageBuild',
    ];
  }

  /**
   * Reacts on term page build.
   *
   * @param \Drupal\taxonomy_custom_controller\Event\TermPageBuildEvent $event
   *   The event.
   */
  public function onTermPageBuild(TermPageBuildEvent $event): void {
    $taxonomy_term = $event->getTaxonomyTerm();

    if ($taxonomy_term->bundle() !== 'tags') {
      return;
    }

    $controller = $this
      ->classResolver
      ->getInstanceFromDefinition(TagController::class);
    \assert($controller instanceof TagControllerInterface);
    $event->setBuildArray($controller->page($taxonomy_term));
  }

}
