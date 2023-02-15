<?php declare(strict_types = 1);

namespace Drupal\niklan\Utility;

use Drupal\Component\Transliteration\PhpTransliteration;

/**
 * Class Text with simple string utility.
 */
final class Anchor {

  /**
   * The cache of generated anchors.
   */
  protected static array $cache = [];

  /**
   * Indicated incremental anchors.
   */
  public const COUNTER = 1;

  /**
   * Indicates reusable anchors.
   */
  public const REUSE = 2;

  /**
   * Generates anchor for string.
   *
   * @param string $text
   *   The string to generate anchor from.
   * @param int $mode
   *   The mode used when anchor for provided text and id is already exists.
   *   Available values:
   *   - COUNTER: Each new anchor will have suffix "-N".
   *   - REUSE: Will return already generated anchor.
   *
   * @return string
   *   The anchor string.
   */
  public static function generate(string $text, int $mode = self::COUNTER): string {
    $anchor = self::prepareAnchor($text);

    return match ($mode) {
      self::REUSE => $anchor,
      self::COUNTER => self::generateWithCounter($anchor),
    };
  }

  /**
   * Generates anchor with counter mode.
   *
   * @param string $anchor
   *   The processed anchor.
   *
   * @return string
   *   The anchor with counter suffix if needed.
   */
  protected static function generateWithCounter(string $anchor): string {
    $iteration = 0;

    while (TRUE) {
      $key = "$anchor:$iteration";

      if (\array_key_exists($key, self::$cache)) {
        $iteration++;

        continue;
      }

      return self::$cache[$key] = $iteration ? "$anchor-$iteration" : $anchor;
    }
  }

  /**
   * Prepares anchor string.
   *
   * @param string $text
   *   The text from which to create an anchor.
   *
   * @return string
   *   The processed anchor.
   */
  protected static function prepareAnchor(string $text): string {
    $transliteration = new PhpTransliteration();
    $anchor = $transliteration->transliterate($text);
    $anchor = \strtolower($anchor);
    $anchor = \trim($anchor);
    // Replace all spaces with dash.
    $anchor = \preg_replace("/[\s_]/", '-', $anchor);
    // Remove everything else. Only alphabet, numbers and dash is allowed.
    $anchor = \preg_replace("/[^0-9a-z-]/", '', $anchor);

    // Replace multiple dashes with single. F.e. "Title with - dash".
    return \preg_replace('/-{2,}/', '-', $anchor);
  }

}
