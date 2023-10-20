<?php declare(strict_types=1);

namespace Drupal\niklan\EventSubscriber;

use Drupal\external_content\Event\HtmlPostParseEvent;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\niklan\CommonMark\Adapter\EmbedAdapter;
use Drupal\niklan\CommonMark\Extension\NiklanMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class BlogContentSyncEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      HtmlPreParseEvent::class => ['convertMarkdown'],
    ];
  }

  /**
   * {@selfdoc}
   */
  public function convertMarkdown(HtmlPreParseEvent $event): void {
    if (!$event->getHtml()->getFile()->getData()->has('is_markdown')) {
      return;
    }

    // @todo Use BlogMarkdownConverter.
    $html = $converter->convert($event->getHtml()->getContent());
    $event->getHtml()->setContent($html->getContent());
    dump($event->getHtml()->getContent());
  }

}
