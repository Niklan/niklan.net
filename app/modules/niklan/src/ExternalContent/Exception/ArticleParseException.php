<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Exception;

final class ArticleParseException extends \RuntimeException {

  public function __construct(string $file_path, string $message) {
    parent::__construct(\sprintf('Article parse failed from %s because of: %s', $file_path, $message));
  }

}
