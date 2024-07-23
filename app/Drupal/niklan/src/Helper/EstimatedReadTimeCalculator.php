<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Node\StringContainerInterface;
use Drupal\external_content\Node\Code;

final class EstimatedReadTimeCalculator {

  /**
   * The expected read speed in words per minute.
   *
   * @see https://en.wikipedia.org/wiki/Words_per_minute
   */
  protected int $wordsPerMinute = 143;

  public function calculate(NodeInterface $content): int {
    $estimated_read_time = 0;
    $this->calculateRecursive($content, $estimated_read_time);

    if ($estimated_read_time > 60) {
      return (int) \ceil($estimated_read_time / 60);
    }

    return 0;
  }

  protected function calculateEstimatedReadTime(int $words_count, int|float $read_time_multiplier = 1): int|float {
    return \floor($words_count * $read_time_multiplier / $this->wordsPerMinute * 60);
  }

  private function calculateRecursive(NodeInterface $node, int|float &$estimated): void {
    foreach ($node->getChildren() as $child) {
      $this->calculateRecursive($child, $estimated);
    }

    if (!($node instanceof StringContainerInterface)) {
      return;
    }

    $read_time_multiplier = match ($node::class) {
      default => 1,
      Code::class => 3,
    };
    $estimated += $this->calculateEstimatedReadTime(
      words_count: \str_word_count($node->getLiteral()),
      read_time_multiplier: $read_time_multiplier,
    );
  }

}
