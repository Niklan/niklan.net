<?php

declare(strict_types=1);

namespace Drupal\app_comment\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Hook\Order\Order;

/**
 * Splits the pager out of the 'comments' variable for comment fields.
 *
 * CommentFormatter nests the pager inside the comment tree (matching core's
 * CommentDefaultFormatter, since field--comment's own preprocessing only
 * forwards a 'comments' variable to the template). This exposes it as its
 * own 'pager' variable instead, so the theme can lay out items and pager
 * independently (e.g. wrap them for "load more").
 */
#[Hook('preprocess_field', order: Order::Last)]
final class PreprocessFieldComment {

  public function __invoke(array &$variables): void {
    // Must run after core's comment_preprocess_field(), which populates
    // 'comments' from the element in the first place — otherwise this runs
    // against a not-yet-populated 'comments' and comment.module's own
    // preprocessing overwrites it (pager included) right afterwards.
    if (($variables['field_type'] ?? NULL) !== 'comment') {
      return;
    }

    $variables['pager'] = $variables['comments']['pager'] ?? NULL;
    unset($variables['comments']['pager']);
  }

}
