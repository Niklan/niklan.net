<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;

/**
 * Provides calculator for estimated read time for paragraphs.
 */
class EstimatedReadTimeCalculator {

  /**
   * Calculates estimated read time for provided paragraph list.
   *
   * @param \Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList $items
   *   The field with paragraph list.
   *
   * @return int
   *   The estimated read time in minutes.
   */
  public static function calculate(EntityReferenceRevisionsFieldItemList $items): int {
    // Average word per minute reading for cyrillic.
    // @see https://en.wikipedia.org/wiki/Words_per_minute
    // Get lower value for Cyrillic because content need to bee comprehend.
    $word_per_minute = 143;
    $estimated_read_time = 0;

    foreach ($items->referencedEntities() as $paragraph) {
      switch ($paragraph->bundle()) {
        case 'text':
          // @todo When moving out of paragraphs, find this broken ones and
          //   check they are really broken. At this point didn't see any
          //   data los with them, they are comes from nowhere.
          if ($paragraph->get('field_body')->isEmpty()) {
            continue 2;
          }
          $word_count = \str_word_count(
            \strip_tags($paragraph->get('field_body')->value),
          );
          // Two time slower because of complexity of texts.
          $estimated_read_time += \floor(
            $word_count / ($word_per_minute / 2) * 60,
          );
          break;

        case 'code':
          if ($paragraph->get('field_body')->isEmpty()) {
            continue 2;
          }
          $word_count = \str_word_count($paragraph->get('field_body')->value);
          // Assumes that code reads two three slower than text.
          $estimated_read_time += \floor(
            $word_count / ($word_per_minute / 3) * 60,
          );
          break;

        case 'image':
          // 10 seconds for image.
          $estimated_read_time += 10;
          break;
      }
    }

    if ($estimated_read_time > 60) {
      return (int) \ceil($estimated_read_time / 60);
    }

    return 0;
  }

}
