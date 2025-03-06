<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Infrastructure;

use Drupal\niklan\ExternalContent\Exception\XmlLoadException;
use Drupal\niklan\ExternalContent\Exception\XmlValidationException;
use Drupal\niklan\Utils\PathHelper;

final readonly class XmlValidator {

  /**
   * @param string $file_path
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlLoadException
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlValidationException
   */
  public function validate(string $file_path): void {
    $document = new \DOMDocument();

    if (!$document->load($file_path)) {
      throw new XmlLoadException($file_path);
    }

    $root = $document->documentElement;
    if (!$root) {
      throw new XmlValidationException('XML does not have a root element.');
    }

    $schemaLocation = $root->getAttributeNS(
      'http://www.w3.org/2001/XMLSchema-instance',
      'noNamespaceSchemaLocation',
    );
    if (!$schemaLocation) {
      throw new XmlValidationException("XML does not have a schemaLocation attribute with schema path.");
    }

    $schemaPath = PathHelper::normalizePath(\dirname($file_path) . '/' . $schemaLocation);

    if (!$document->schemaValidate($schemaPath)) {
      $errors = \libxml_get_errors();
      $error_messages = \array_map(static fn ($error) => \sprintf(
          "[%s] Line %d: %s",
          $error->level === \LIBXML_ERR_WARNING ? "Warning" : "Error",
          $error->line,
          \trim($error->message),
        ), $errors);

      \libxml_clear_errors();
      throw new XmlValidationException("XML is invalid: " . \implode("\n", $error_messages));
    }
  }

}
