<?php

declare(strict_types=1);

namespace Drupal\niklan\Markup\Markdown\Helper;

use League\CommonMark\Parser\Cursor;

/**
 * @ingroup markdown
 */
final class CommonMarkDirectiveHelper {

  // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
  private const string TYPE_REGEX = '/^\s*([a-zA-Z]+)/';
  private const string CSS_SELECTORS_REGEX = '/[#.\-_:a-zA-Z0-9=]+/';
  private const string ATTRIBUTES_KEY_REGEX = '/([a-zA-Z-]+)/';
  private const string ESCAPE_CHAR = '\\';

  /**
   * Parses info string.
   *
   * The info string is:
   * @code
   * name[inline-content](argument){#id .class key=value}
   * @endcode
   *
   * Everything except 'name' is an optional. The space between the groups is
   * allowed, but expected only for container and leaf block directives. All
   * values returned as a raw values without opening and closing character:
   * @code
   * return [
   *   'type' => 'name',
   *   'inline-content' => 'inline-content',
   *   'argument' => 'argument',
   *   'attributes' => '#id .class key=value',
   * ]
   * @endcode
   *
   * Attributes should be parsed by using self::parseExtraAttributes() because
   * current method is only responsible for extracting raw group values and not
   * do any additional processing. This also means, that inline contain with
   * markdown inside won't be converted by this helper.
   *
   * Groups content can use escape character '\' if they are contain the char
   * same as a closing group has. For example, for inline content group, char
   * ']' should be escaped if it is a part of the content. The result will be
   * cleaned from escape character automatically.
   *
   * @return array{
   *   'type': string,
   *   'inline-content': string|null,
   *   'argument': string|null,
   *   'attributes': string|null,
   *   }
   *
   * @see self::parseExtraAttributes()
   */
  public static function parseInfoString(string $info_line): array {
    $cursor = new Cursor($info_line);
    $result = [
      'type' => $cursor->match(self::TYPE_REGEX),
      'inline-content' => NULL,
      'argument' => NULL,
      'attributes' => NULL,
    ];

    // The type should be presented.
    // @see \Drupal\niklan\Markup\Markdown\Parser\LeafBlockDirectiveStartParser::tryStart
    \assert(\is_string($result['type']), 'Directive type must be specified.');

    while (!$cursor->isAtEnd()) {
      match ($cursor->getCurrentCharacter()) {
        default => $cursor->advanceBy(1),
        '[' => self::parseGroup($cursor, ']', $result['inline-content']),
        '(' => self::parseGroup($cursor, ')', $result['argument']),
        '{' => self::parseGroup($cursor, '}', $result['attributes']),
      };
    }

    return $result;
  }

  /**
   * Parses extra attributes.
   *
   * Extra attributes is a set of additional information which can contain ID
   * and class CSS selector, as wel ass key/value pairs.
   * @code
   * #id .class key=value foo="bar baz"
   * @endcode
   *
   * Note that input shouldn't contain opening and closing curly bracket.
   *
   * @return array{
   *   'id': string|null,
   *   'class': array<int<0,max>, string>,
   *   'key-value': string[],
   *   }
   */
  public static function parseExtraAttributes(string $attributes): array {
    $cursor = new Cursor($attributes);
    $result = [
      'id' => NULL,
      'class' => [],
      'key-value' => [],
    ];

    while (!$cursor->isAtEnd()) {
      match ($cursor->getCurrentCharacter()) {
        ' ' => $cursor->advanceBy(1),
        '#' => $result['id'] = $cursor->match(self::CSS_SELECTORS_REGEX),
        '.' => $result['class'][] = $cursor->match(self::CSS_SELECTORS_REGEX),
        default => self::parseKeyValuePairs($cursor, $result['key-value']),
      };
    }

    if ($result['id']) {
      $result['id'] = \str_replace('#', '', $result['id']);
    }

    if ($result['class']) {
      // Filter the class array to remove non-string or empty elements.
      $result['class'] = \array_filter(
        array: $result['class'],
        callback: static fn (?string $class): bool => \is_string($class) && \strlen($class) > 0,
      );
      // Remove CSS notation (dots) from class names.
      \array_walk(
        array: $result['class'],
        callback: static fn (string &$class): string => $class = \str_replace('.', '', $class),
      );
    }

    return $result;
  }

  public static function flattenExtraAttributes(array $extra_attributes): array {
    $attributes = $extra_attributes;

    if (\array_key_exists('key-value', $attributes)) {
      foreach ($attributes['key-value'] as $key => $value) {
        $attributes["data-$key"] = $value;
      }

      unset($attributes['key-value']);
    }

    return $attributes;
  }

  private static function parseGroup(Cursor $cursor, string $closing_char, ?string &$result): void {
    if (!$result) {
      $result = '';
    }

    $cursor->advanceBy(1);

    do {
      $result .= $cursor->getCurrentCharacter();
      $cursor->advanceBy(1);
    } while (!$cursor->isAtEnd() && !($cursor->getCurrentCharacter() === $closing_char && $cursor->peek(-1) !== self::ESCAPE_CHAR));

    // Remove escaping.
    $result = \str_replace("\\$closing_char", $closing_char, $result);
  }

  private static function parseKeyValuePairs(Cursor $cursor, array &$key_value): void {
    $key = $cursor->match(self::ATTRIBUTES_KEY_REGEX);

    // Skip '='.
    $cursor->advanceBy(1);
    $has_string_opening = $cursor->getCurrentCharacter() === '"';

    if ($has_string_opening) {
      $cursor->advanceBy(1);
    }

    $value = self::parseValue($cursor, $has_string_opening);

    if ($has_string_opening) {
      // Skip closing '"'.
      $cursor->advanceBy(1);
    }

    $key_value[$key] = $value;
  }

  private static function parseValue(Cursor $cursor, bool $has_string_opening): string {
    $value = '';

    do {
      $value .= $cursor->getCurrentCharacter();
      $cursor->advanceBy(1);
    } while (self::shouldContinueParseValue($cursor, $has_string_opening));

    return $value;
  }

  private static function shouldContinueParseValue(Cursor $cursor, bool $has_string_opening): bool {
    if ($cursor->isAtEnd()) {
      return FALSE;
    }

    if ($has_string_opening) {
      return !($cursor->getCurrentCharacter() === '"' && $cursor->peek(-1) !== self::ESCAPE_CHAR);
    }

    return $cursor->getCurrentCharacter() !== ' ';
  }

}
