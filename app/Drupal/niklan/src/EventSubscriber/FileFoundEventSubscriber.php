<?php declare(strict_types = 1);

namespace Drupal\niklan\EventSubscriber;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\external_content\Event\FileFoundEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@selfdoc}
 *
 * @ingroup external_content
 */
final class FileFoundEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      FileFoundEvent::class => [
        ['onBlogSourceFileFound'],
      ],
    ];
  }

  /**
   * {@selfdoc}
   */
  public function onBlogSourceFileFound(FileFoundEvent $event): void {
    if ($event->environment->id !== 'blog') {
      return;
    }

    // Extract metadata from Front Matter.
    $front_matter = FrontMatter::create($event->file->contents());
    $event->file->data()->set('front_matter', $front_matter->getData());
  }

}
