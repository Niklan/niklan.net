<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

final class ElementInfoAlter {

  public function __invoke(array &$info): void {
    if (!\array_key_exists('page', $info)) {
      return;
    }

    unset($info['page']['#theme_wrappers']['off_canvas_page_wrapper']);
  }

}
