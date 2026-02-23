<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'niklan_contact' => [
        'variables' => [
          'description' => NULL,
          'email' => NULL,
          'telegram' => NULL,
        ],
      ],
      'niklan_support' => [
        'variables' => [
          'description' => NULL,
          'donate_url' => NULL,
        ],
      ],
      'niklan_about' => [
        'variables' => [
          'photo_uri' => NULL,
          'heading' => NULL,
          'subtitle' => NULL,
          'summary' => NULL,
          'description' => NULL,
        ],
      ],
      'niklan_services' => [
        'variables' => [
          'description' => NULL,
          'hourly_rate' => NULL,
        ],
      ],
      'niklan_comment_list' => [
        'variables' => [
          'heading' => NULL,
          'items' => [],
        ],
      ],
      'niklan_blog_list' => [
        'variables' => [
          'items' => [],
          'pager' => NULL,
        ],
      ],
      'niklan_tag_list' => [
        'variables' => [
          'items' => [],
        ],
      ],
      'niklan_lightbox_responsive_image' => [
        'variables' => [
          'uri' => NULL,
          'thumbnail_responsive_image_style_id' => NULL,
          'lightbox_image_style_id' => NULL,
          'alt' => NULL,
          'title' => NULL,
          'attributes' => [],
        ],
      ],
      'niklan_comment_thread' => [
        'variables' => [
          'depth' => 0,
          'thread_id' => NULL,
          'comments' => [],
        ],
      ],
      'niklan_home' => [
        'variables' => [
          'sections' => [],
        ],
      ],
      'niklan_blog_preview_list' => [
        'variables' => [
          'heading' => NULL,
          'items' => [],
        ],
      ],
      'niklan_comment_reply_page' => [
        'variables' => [
          'children' => [],
        ],
      ],
      'niklan_home_intro' => [
        'variables' => [
          'heading' => NULL,
          'description' => NULL,
        ],
      ],
      'niklan_home_cards' => [
        'variables' => [
          'cards' => [],
        ],
      ],
      'niklan_article_banner' => [
        'variables' => [
          'poster_base64' => NULL,
          'text_lines' => [],
          'comment_count' => 0,
          'created' => 0,
        ],
      ],
      'niklan_sitemap' => [
        'variables' => [
          'sitemap' => [],
        ],
      ],
    ];
  }

}
