<?php

declare(strict_types = 1);

namespace Drupal\external_content\Dto;

/**
 * Represents a single content in a different languages.
 */
final class ExternalContent {

  /**
   * The content translations.
   *
   * @var \Drupal\external_content\Dto\ParsedSourceFile[]
   */
  protected array $translations = [];

  /**
   * Constructs a new ExternalContent object.
   *
   * @param string $id
   *   The content identifier.
   */
  public function __construct(protected string $id,) {}

  /**
   * Gets a content ID.
   *
   * @return string
   *   The content ID.
   */
  public function id(): string {
    return $this->id;
  }

  /**
   * Adds content translation.
   *
   * @param string $language
   *   The content langcode.
   * @param \Drupal\external_content\Dto\ParsedSourceFile $file
   *   The file with content and metadata.
   *
   * @return $this
   */
  public function addTranslation(string $language, ParsedSourceFile $file): self {
    $this->translations[$language] = $file;

    return $this;
  }

  /**
   * Gets translation.
   *
   * @param string $language
   *   The langcode.
   *
   * @return \Drupal\external_content\Dto\ParsedSourceFile|null
   *   The parsed source file.
   */
  public function getTranslation(string $language): ?ParsedSourceFile {
    return $this->hasTranslation($language)
      ? $this->translations[$language]
      : NULL;
  }

  /**
   * Checks for specific translation.
   *
   * @param string $language
   *   The langcode.
   *
   * @return bool
   *   TRUE if translation for provided language is exists.
   */
  public function hasTranslation(string $language): bool {
    return \array_key_exists($language, $this->translations);
  }

}
