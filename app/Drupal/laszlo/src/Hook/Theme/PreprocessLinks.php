<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

final readonly class PreprocessLinks {

  public function __invoke(array &$variables): void {
    match ($variables['theme_hook_original']) {
      default => NULL,
      'links__comment' => $this->preprocessComment($variables),
    };
  }

  private function preprocessComment(array &$variables): void {
    $links_to_simplify = ['comment-delete', 'comment-edit', 'comment-reply'];

    foreach ($links_to_simplify as $link_to_simplify) {
      if (array_key_exists($link_to_simplify, $variables['links'])) {
        $key = \str_replace('-', '_', $link_to_simplify);
        $variables[$key] = [
          'label' => $variables['links'][$link_to_simplify]['text'],
          'url' => $variables['links'][$link_to_simplify]['link']['#url']->toString(),
        ];
      }
    }
  }

}
