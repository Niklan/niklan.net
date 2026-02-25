<?php

declare(strict_types=1);

namespace Drupal\app_blog\Hook\Core;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

#[Hook('page_attachments_alter')]
final class PageAttachmentsAlter {

  public function __invoke(array &$attachments): void {
    $rss_url = Url::fromRoute('app_blog.rss_feed')->setAbsolute()->toString();

    $attachments['#attached']['html_head_link'][] = [
      [
        'rel' => 'alternate',
        'type' => 'application/rss+xml',
        'title' => 'RSS',
        'href' => (string) $rss_url,
      ],
      TRUE,
    ];
  }

}
