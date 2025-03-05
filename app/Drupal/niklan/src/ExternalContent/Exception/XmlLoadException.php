<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Exception;

final class XmlLoadException extends \RuntimeException {

  /**
   * @param non-empty-string $file_path
   */
  public function __construct(string $file_path) {
    parent::__construct(\sprintf('Failed to load XML file: %s', $file_path));
  }

}
