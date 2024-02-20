<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

use League\CommonMark\Parser\Cursor;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class CommonMarkDirectiveHelper {

  /**
   * {@selfdoc}
   */
  // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
  private const string TYPE_REGEX = '/^\s*([a-zA-Z]+)/';

  /**
   * {@selfdoc}
   */
  // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
  private const string CSS_SELECTORS_REGEX = '/[#.\-_:a-zA-Z0-9=]+/';

  /**
   * {@selfdoc}
   */
  // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
  private const string ATTRIBUTES_KEY_REGEX = '/([a-zA-Z-]+)/';

  /**
   * {@selfdoc}
   */
  // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
  private const string ESCAPE_CHAR = '\\';

  /**
   * {@selfdoc}
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
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
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
   *   'class': string[],
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
      \array_walk(
        $result['class'],
        static fn (string &$class) => $class = \str_replace('.', '', $class),
      );
    }

    return $result;
  }

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
  private static function parseValue(Cursor $cursor, bool $has_string_opening): string {
    $value = '';

    do {
      $value .= $cursor->getCurrentCharacter();
      $cursor->advanceBy(1);
    } while (self::shouldContinueParseValue($cursor, $has_string_opening));

    return $value;
  }

  /**
   * {@selfdoc}
   */
  private static function shouldContinueParseValue(Cursor $cursor, bool $has_string_opening): bool {
    if ($cursor->isAtEnd()) {
      return FALSE;
    }

    if ($has_string_opening) {
      return !($cursor->getCurrentCharacter() === '"' && $cursor->peek(-1) !== self::ESCAPE_CHAR);
    }

    return $cursor->getCurrentCharacter() !== ' ';
  }

  /**
   * Builds an array with attributes for HTML element based on info line.
   *
   * Note that inline content '[]' is not part of attributes.
   */
  public static function prepareElementAttributes(string $info_line): array {
    $info = self::parseInfoString($info_line);
    $attributes = [
      'data-type' => $info['type'],
    ];

    if ($info['argument']) {
      $attributes['data-argument'] = $info['argument'];
    }

    if ($info['attributes']) {
      $extra_attributes = self::parseExtraAttributes($info['attributes']);
      self::prepareElementExtraAttributes($attributes, $extra_attributes);
    }

    return $attributes;
  }

  /**
   * {@selfdoc}
   */
  private static function prepareElementExtraAttributes(array &$attributes, array $extra_attributes): void {
    if ($extra_attributes['id']) {
      $attributes['id'] = $extra_attributes['id'];
    }

    if ($extra_attributes['class']) {
      $attributes['class'] = \implode(' ', $extra_attributes['class']);
    }

    foreach ($extra_attributes['key-value'] as $key => $value) {
      $attributes[$key] = $value;
    }
  }

}
