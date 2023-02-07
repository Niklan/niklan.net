<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\DataType;

use Drupal\Core\TypedData\Plugin\DataType\StringData;
use Drupal\external_content\Dto\ParsedSourceFile as ParsedSourceFileDto;

/**
 * Provides parsed source file typed data.
 *
 * @DataType(
 *   id = "external_content_parsed_source_file",
 *   label = @Translation("Parsed source file"),
 * )
 */
final class ParsedSourceFile extends StringData {

  /**
   * Gets parsed source file.
   *
   * @return \Drupal\external_content\Dto\ParsedSourceFile|null
   *   The content document object.
   */
  public function getParsedSourceFile(): ?ParsedSourceFileDto {
    if (empty($this->value)) {
      return NULL;
    }

    return $this->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE): void {
    if (!$value instanceof ParsedSourceFileDto) {
      return;
    }
    $this->setParsedSourceFile($value, $notify);
  }

  /**
   * Sets parsed source file value.
   *
   * @param \Drupal\external_content\Dto\ParsedSourceFile $parsed_source_file
   *   The parsed source file.
   * @param bool $notify
   *   Indicates should parent be notified.
   */
  public function setParsedSourceFile(ParsedSourceFileDto $parsed_source_file, bool $notify = TRUE): void {
    $this->value = $parsed_source_file;
    // Notify the parent of any changes.
    if ($notify && isset($this->parent)) {
      $this->parent->onChange($this->name);
    }
  }

}
