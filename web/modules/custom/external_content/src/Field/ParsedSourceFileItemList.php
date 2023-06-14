<?php declare(strict_types = 1);

namespace Drupal\external_content\Field;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Provides a field item list for parsed source file field type.
 */
final class ParsedSourceFileItemList extends FieldItemList {

  /**
   * {@inheritdoc}
   */
  public function equals(FieldItemListInterface $list_to_compare): bool {
    // PHP fail to compare big trees by compare two objects and throws exception
    // with recursive nesting. To simplify it, value is serialized before
    // compare.
    $callback = static fn (array &$values): string => $values['value'] = \serialize($values['value']);

    $value_a = $this->getValue();
    $value_b = $list_to_compare->getValue();

    \array_walk($value_a, $callback);
    \array_walk($value_b, $callback);

    return $value_a === $value_b;
  }

}
