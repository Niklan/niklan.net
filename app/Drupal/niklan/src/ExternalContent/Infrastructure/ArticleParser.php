<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Infrastructure;

use Drupal\niklan\ExternalContent\Domain\BlogArticle;
use Drupal\niklan\Utils\PathHelper;

final readonly class ArticleParser {

  public function parseFromFile(string $file_path): BlogArticle {
    $document = new \DOMDocument();

    if (!$document->load($file_path)) {
      throw new \Exception(\sprintf('Failed to load XML file: %s', $file_path));
    }

    $this->validateXmlAgainstXsd($document, $file_path);
    \dump($file_path);
    // @todo Complete
    die;
  }

  private function validateXmlAgainstXsd(\DOMDocument $document, string $file_path): void {
    $root = $document->documentElement;
    if (!$root) {
      throw new \Exception("XML doesn't have a root node");
    }

    $schema_location = $root->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'noNamespaceSchemaLocation');
    if (!$schema_location) {
      throw new \Exception("XML doesn't have a schema location");
    }

    $schema_location = PathHelper::normalizePath(\dirname($file_path) . '/' . $schema_location);
    if (!$document->schemaValidate($schema_location)) {
      $errors = \libxml_get_errors();
      $errorMessages = [];

      foreach ($errors as $error) {
        $errorMessages[] = \sprintf(
          "[%s] Line %d: %s",
          $error->level === \LIBXML_ERR_WARNING ? "Warning" : "Error",
          $error->line,
          \trim($error->message),
        );
      }

      \libxml_clear_errors();
      throw new \Exception("XML not validates against schema:\n" . \implode("\n", $errorMessages));
    }
  }

}
