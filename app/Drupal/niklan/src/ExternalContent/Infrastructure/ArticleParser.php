<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Infrastructure;

use Drupal\niklan\ExternalContent\Domain\BlogArticle;

final readonly class ArticleParser {

  public function parseFromFile(string $file_path): BlogArticle {
    $dom = new \DOMDocument();

    if (!$dom->load($file_path)) {
      throw new \Exception(\sprintf('Failed to load XML file: %s', $file_path));
    }

    dump($file_path);
    // @todo Complete
    die;
  }

}
