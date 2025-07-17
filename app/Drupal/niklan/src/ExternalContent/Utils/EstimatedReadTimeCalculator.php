<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Utils;

use Drupal\external_content\Nodes\Node;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\CodeBlock;

final class EstimatedReadTimeCalculator {

  private const int WORDS_PER_MINUTE = 143;
  private const int SECONDS_IN_MINUTE = 60;
  private const array CONTENT_TYPE_MULTIPLIERS = [
    CodeBlock::class => 3,
  ];

  public function calculateTotalTime(Node $content): int {
    $totalSeconds = $this->calculateNodeTime($content);
    return $this->convertToMinutes($totalSeconds);
  }

  private function calculateNodeTime(Node $node): int {
    $time = 0;

    foreach ($node->getChildren() as $child) {
      $time += $this->calculateNodeTime($child);
    }

    if ($node instanceof \Stringable) {
      $time += $this->calculateNodeSelfTime($node);
    }

    return $time;
  }

  private function calculateNodeSelfTime(\Stringable $node): int {
    $word_count = \str_word_count((string) $node);
    $multiplier = self::CONTENT_TYPE_MULTIPLIERS[$node::class] ?? 1;
    return (int) \floor($word_count * $multiplier / self::WORDS_PER_MINUTE * self::SECONDS_IN_MINUTE);
  }

  private function convertToMinutes(int $total_seconds): int {
    if ($total_seconds === 0) {
      return 0;
    }

    $minutes = (int) \ceil($total_seconds / self::SECONDS_IN_MINUTE);
    return \max(1, $minutes);
  }

}
