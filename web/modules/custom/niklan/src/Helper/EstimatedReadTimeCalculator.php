<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Provides calculator for estimated read time for paragraphs.
 */
final class EstimatedReadTimeCalculator {

  /**
   * The expected read speed in words per minute.
   *
   * @see https://en.wikipedia.org/wiki/Words_per_minute
   */
  protected int $wordsPerMinute = 143;

  /**
   * Calculates estimated read time for provided paragraph list.
   *
   * @param \Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList $items
   *   The field with paragraph list.
   *
   * @return int
   *   The estimated read time in minutes.
   */
  public function calculate(EntityReferenceRevisionsFieldItemList $items): int {
    $estimated_read_time = 0;

    foreach ($items->referencedEntities() as $paragraph) {
      \assert($paragraph instanceof ParagraphInterface);
      $estimated_read_time += match ($paragraph->bundle()) {
        default => 0,
        'text' => $this->calculateTextParagraph($paragraph),
        'code' => $this->calculateCodeParagraph($paragraph),
        'image' => 10,
      };
    }

    if ($estimated_read_time > 60) {
      return (int) \ceil($estimated_read_time / 60);
    }

    return 0;
  }

  /**
   * Calculates read time for 'text' paragraph.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph entity.
   *
   * @return int|float
   *   The estimated read time.
   */
  protected function calculateTextParagraph(ParagraphInterface $paragraph): int|float {
    if ($paragraph->get('field_body')->isEmpty()) {
      return 0;
    }

    $content = $paragraph->get('field_body')->first()->getString();
    $content = \strip_tags($content);
    $words_count = \str_word_count($content);

    return $this->calculateEstimatedReadTime($words_count, 2);
  }

  /**
   * Calculates read time for 'code' paragraph.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph entity.
   *
   * @return int|float
   *   The estimated read time.
   */
  protected function calculateCodeParagraph(ParagraphInterface $paragraph): int|float {
    if ($paragraph->get('field_body')->isEmpty()) {
      return 0;
    }

    $content = $paragraph->get('field_body')->first()->getString();
    $words_count = \str_word_count($content);

    return $this->calculateEstimatedReadTime($words_count, 3);
  }

  /**
   * Calculates estimated read time on words count.
   *
   * @param int $words_count
   *   The words count.
   * @param int|float $multiplier
   *   The speed read multiplier. 2 - means that read time for that part is
   *   expected to be two times slower that usual text.
   *
   * @return int|float
   *   The estimated read time in seconds.
   */
  protected function calculateEstimatedReadTime(int $words_count, int|float $multiplier = 1): int|float {
    return \floor($words_count * $multiplier / $this->wordsPerMinute * 60);
  }

}
