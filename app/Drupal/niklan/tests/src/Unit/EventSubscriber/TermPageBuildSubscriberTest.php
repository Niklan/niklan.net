<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\EventSubscriber;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\niklan\Content\Tag\EventSubscriber\TermPageBuild;
use Drupal\niklan\Controller\TagControllerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy_custom_controller\Event\TaxonomyCustomControllerEvents;
use Drupal\taxonomy_custom_controller\Event\TermPageBuildEvent;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides a test for term page build subscriber.
 */
final class TermPageBuildSubscriberTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that subscriber properly subscribed.
   */
  public function testSubscriber(): void {
    $subscribed_events = TermPageBuild::getSubscribedEvents();

    self::assertArrayHasKey(
      TaxonomyCustomControllerEvents::PAGE_BUILD,
      $subscribed_events,
    );
  }

  /**
   * Tests that subscribed doesn't affect vocabularies other than 'tags'.
   */
  public function testOnTermPageBuildWithWrongTerm(): void {
    $term = $this->prophesize(TermInterface::class);
    $term->bundle()->willReturn('not_tags');

    $subscriber = new TermPageBuild($this->getClassResolver());
    $event = new TermPageBuildEvent($term->reveal());
    $subscriber->onTermPageBuild($event);

    self::assertEquals([], $event->getBuildArray());
  }

  /**
   * Tests that subscriber set build result.
   */
  public function testOnTermPageBuildWithValidTerm(): void {
    $term = $this->prophesize(TermInterface::class);
    $term->bundle()->willReturn('tags');

    $subscriber = new TermPageBuild($this->getClassResolver());
    $event = new TermPageBuildEvent($term->reveal());
    $subscriber->onTermPageBuild($event);

    self::assertEquals(['#markup' => 'Hello, World!'], $event->getBuildArray());
  }

  /**
   * Prepares class resolver for event testing.
   */
  protected function getClassResolver(): ClassResolverInterface {
    $tag_controller = $this->prophesize(TagControllerInterface::class);
    $tag_controller->page(Argument::any())->willReturn([
      '#markup' => 'Hello, World!',
    ]);

    $class_resolver = $this->prophesize(ClassResolverInterface::class);
    $class_resolver
      ->getInstanceFromDefinition(Argument::any())
      ->willReturn($tag_controller->reveal());

    return $class_resolver->reveal();
  }

}
