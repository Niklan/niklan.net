<?php

use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\RegexHelper;

$line = 'name [inline-content\] 123] (argument\)) {#id .class-a .class-b key=value key-b="value with space and bracket\}"}';
$cursor = new Cursor($line);

$escape_char = '\\';
$type = '';
$inline_content = NULL;
$argument = NULL;
$attributes = NULL;

do {
  $type .= $cursor->getCurrentCharacter();
  $cursor->advanceBy(1);
}
while ($cursor->getCurrentCharacter() != ' ');

while (!$cursor->isAtEnd()) {
  match ($cursor->getCurrentCharacter()) {
    default => $cursor->advanceBy(1),
    '[' => parseSection($cursor, ']', $inline_content),
    '(' => parseSection($cursor, ')', $argument),
    '{' => parseSection($cursor, '}', $attributes),
  };
}

function parseSection(Cursor $cursor, string $closing_char, ?string &$result) {
  if (!$result) {
    $result = '';
  }

  $cursor->advanceBy(1);
  do {
    $result .= $cursor->getCurrentCharacter();
    $cursor->advanceBy(1);
  } while (!$cursor->isAtEnd() && !($cursor->getCurrentCharacter() === $closing_char && $cursor->peek(-1) !== '\\'));

  // Remove escaping.
  $result = str_replace("\\$closing_char", $closing_char, $result);
}

dump($type);
dump($inline_content);
dump($argument);
dump($attributes);

$cursor = new Cursor($attributes);
$identifier = NULL;
$selectors = [];
$key_value = [];

while (!$cursor->isAtEnd()) {
  match ($cursor->getCurrentCharacter()) {
    ' ' => $cursor->advanceBy(1),
    '#' => $identifier = $cursor->match('/[#.\-_:a-zA-Z0-9=]+/'),
    '.' => $selectors[] = $cursor->match('/[#.\-_:a-zA-Z0-9=]+/'),
    default => parseKeyValue($cursor, $key_value),
  };
}

function parseKeyValue(Cursor $cursor, array &$key_value) {
  $key = '';
  $value = '';

  do {
    $key .= $cursor->getCurrentCharacter();
    $cursor->advanceBy(1);
  } while ($cursor->getCurrentCharacter() !== '=' && $cursor->getCurrentCharacter());

  // Skip '='
  $cursor->advanceBy(1);
  $has_string_opening = $cursor->getCurrentCharacter() === '"';

  if ($has_string_opening) {
    $cursor->advanceBy(1);
  }

  do {
    $value .= $cursor->getCurrentCharacter();
    $cursor->advanceBy(1);

    if ($has_string_opening) {
      $should_continue = !($cursor->getCurrentCharacter() === '"' && $cursor->peek(-1) !== '\\');
    }
    else {
      $should_continue = $cursor->getCurrentCharacter() !== ' ';
    }
  } while ($should_continue);

  if ($has_string_opening) {
    // Skip closing '"'.
    $cursor->advanceBy(1);
  }

  $key_value[$key] = $value;
}

dump($identifier);
dump($selectors);
dump($key_value);
