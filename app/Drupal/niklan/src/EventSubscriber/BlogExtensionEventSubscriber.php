<?php declare(strict_types = 1);

namespace Drupal\niklan\EventSubscriber;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\external_content\Event\FileFoundEvent;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@selfdoc}
 *
 * @ingroup external_content
 */
final class BlogExtensionEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      FileFoundEvent::class => [
        ['onBlogSourceFileFound'],
      ],
      HtmlPreParseEvent::class => [
        ['onHtmlPreParseEvent'],
      ],
    ];
  }

  /**
   * {@selfdoc}
   */
  public function onBlogSourceFileFound(FileFoundEvent $event): void {
    if ($event->environment->id() !== 'blog') {
      return;
    }

    // Extract metadata from Front Matter.
    $front_matter = FrontMatter::create($event->file->contents());
    $event->file->data()->set('front_matter', $front_matter->getData());
  }

  /**
   * {@selfdoc}
   */
  public function onHtmlPreParseEvent(HtmlPreParseEvent $event): void {
    if ($event->environment->id() !== 'blog') {
      return;
    }

    // Remove Front Matter from the content.
    $front_matter = FrontMatter::create($event->content);
    $event->content = $front_matter->getContent();
  }

}
