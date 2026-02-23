<?php

declare(strict_types=1);

namespace Drupal\app_blog\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'app_blog_list' => [
        'variables' => [
          'items' => [],
          'pager' => NULL,
        ],
      ],
      'app_blog_preview_list' => [
        'variables' => [
          'heading' => NULL,
          'items' => [],
        ],
      ],
      'app_blog_article_banner' => [
        'variables' => [
          'poster_base64' => NULL,
          'text_lines' => [],
          'comment_count' => 0,
          'created' => 0,
        ],
      ],
      'app_blog_lightbox_responsive_image' => [
        'variables' => [
          'uri' => NULL,
          'thumbnail_responsive_image_style_id' => NULL,
          'lightbox_image_style_id' => NULL,
          'alt' => NULL,
          'title' => NULL,
          'attributes' => [],
        ],
      ],
    ];
  }

}
