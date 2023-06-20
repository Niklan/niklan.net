<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 *
 */

final class ExternalContentHtml {

  protected array $data = [];

  /**
 *
 */
  public function __construct(
    protected ExternalContentFile $file,
    protected string $content,
  ) {}

  /**
 *
 */
  public function getExternalContentFile(): ExternalContentFile {
    return $this->file;
  }

  /**
 *
 */
  public function setContent(string $content): self {
    $this->content = $content;

    return $this;
  }

  /**
 *
 */
  public function getContent(): string {
    return $this->content;
  }

  /**
 *
 */
  public function setData(string $key, mixed $value): self {
    $this->data[$key] = $value;

    return $this;
  }

  /**
 *
 */
  public function hasData(string $key): bool {
    return \array_key_exists($key, $this->data);
  }

  /**
 *
 */
  public function getData(string $key): mixed {
    if (!$this->hasData($key)) {
      return NULL;
    }

    return $this->data[$key];
  }

}
