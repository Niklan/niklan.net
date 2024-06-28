<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Node\StringContainerInterface;
use Drupal\external_content\Node\Code;

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
   * {@selfdoc}
   */
  public function calculate(NodeInterface $content): int {
    $estimated_read_time = 0;
    $this->calculateRecursive($content, $estimated_read_time);

    if ($estimated_read_time > 60) {
      return (int) \ceil($estimated_read_time / 60);
    }

    return 0;
  }

  /**
   * {@selfdoc}
   */
  private function calculateRecursive(NodeInterface $node, int|float &$estimated): void {
    foreach ($node->getChildren() as $child) {
      $this->calculateRecursive($child, $estimated);
    }

    if (!($node instanceof StringContainerInterface)) {
      return;
    }

    $words_count = \str_word_count($node->getLiteral());
    $multiplier = match ($node::class) {
      default => 1,
      Code::class => 3,
    };
    $estimated += $this->calculateEstimatedReadTime($words_count, $multiplier);
  }

  /**
   * Calculates estimated read time on words count.
   *
   * @param int $words_count
   *   The words count.
   * @param int|float $multiplier
   *   The speed read multiplier. 2 - means that read time for that part is
   *   expected to be two times slower that usual text.
   */
  protected function calculateEstimatedReadTime(int $words_count, int|float $multiplier = 1): int|float {
    return \floor($words_count * $multiplier / $this->wordsPerMinute * 60);
  }

}
