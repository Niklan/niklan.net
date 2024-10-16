<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\taxonomy\TermInterface;

final readonly class ThemeSuggestionsTaxonomyTermAlter {

  public function __invoke(array &$suggestions, array &$variables): void {
    $taxonomy_term = $variables['elements']['#taxonomy_term'];
    \assert($taxonomy_term instanceof TermInterface);
    $view_mode = $variables['elements']['#view_mode'];
    \array_unshift($suggestions, 'taxonomy_term__' . $view_mode);
    // Theme suggestion taxonomy-term--TYPE--VIEW must be inserted right after
    // default suggestion taxonomy-term--TYPE.
    foreach ($suggestions as $key => $suggestion) {
      if ($suggestion === 'taxonomy_term__' . $taxonomy_term->bundle()) {
        \array_splice($suggestions, $key + 1, 0, 'taxonomy_term__' . $taxonomy_term->bundle() . '__' . $view_mode);
        break;
      }
    }
    $suggestions[] = 'taxonomy_term__' . $taxonomy_term->id() . '__' . $view_mode;
  }

}
