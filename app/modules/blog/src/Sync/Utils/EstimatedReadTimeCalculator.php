<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Utils;

final class EstimatedReadTimeCalculator {

  private const int WORDS_PER_MINUTE = 143;
  private const int SECONDS_IN_MINUTE = 60;
  private const int CODE_MULTIPLIER = 3;

  public function calculate(string $html): int {
    $code_seconds = $this->calculateCodeTime($html);
    $text_without_code = \preg_replace('/<pre[\s>].*?<\/pre>/s', '', $html) ?? $html;
    $text_seconds = $this->calculateTextTime(\strip_tags($text_without_code));

    return $this->convertToMinutes($code_seconds + $text_seconds);
  }

  private function calculateCodeTime(string $html): int {
    \preg_match_all('/<pre[\s>].*?<\/pre>/s', $html, $matches);
    $code_text = \implode(' ', \array_map('strip_tags', $matches[0]));
    $word_count = \str_word_count($code_text);

    return (int) \floor($word_count * self::CODE_MULTIPLIER / self::WORDS_PER_MINUTE * self::SECONDS_IN_MINUTE);
  }

  private function calculateTextTime(string $text): int {
    $word_count = \str_word_count($text);
    return (int) \floor($word_count / self::WORDS_PER_MINUTE * self::SECONDS_IN_MINUTE);
  }

  private function convertToMinutes(int $total_seconds): int {
    if ($total_seconds === 0) {
      return 0;
    }

    return \max(1, (int) \ceil($total_seconds / self::SECONDS_IN_MINUTE));
  }

}
