<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Exception;

final class ArticleParseException extends \RuntimeException {

  /**
   * @param non-empty-string $file_path
   * @param non-empty-string $message
   */
  public function __construct(string $file_path, string $message) {
    parent::__construct(\sprintf('Article parse failed from %s because of: %s', $file_path, $message));
  }

}
