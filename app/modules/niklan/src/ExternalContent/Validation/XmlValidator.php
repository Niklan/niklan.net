<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Validation;

use Drupal\niklan\ExternalContent\Exception\XmlLoadException;
use Drupal\niklan\ExternalContent\Exception\XmlValidationException;
use Drupal\app_contract\Utils\PathHelper;

final readonly class XmlValidator {

  private const string XML_SCHEMA_INSTANCE_NS = 'http://www.w3.org/2001/XMLSchema-instance';

  /**
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlLoadException
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlValidationException
   */
  public function validate(string $file_path): void {
    $document = $this->loadXmlDocument($file_path);
    $root_element = $this->getRootElement($document);
    $schema_path = $this->resolveSchemaPath($file_path, $this->getSchemaLocation($root_element));
    $this->validateAgainstSchema($document, $schema_path);
  }

  /**
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlLoadException
   */
  private function loadXmlDocument(string $file_path): \DOMDocument {
    $document = new \DOMDocument();
    if (!$document->load($file_path)) {
      throw new XmlLoadException("Failed to load XML file: $file_path");
    }

    return $document;
  }

  /**
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlValidationException
   */
  private function getRootElement(\DOMDocument $document): \DOMElement {
    if (!$document->documentElement) {
      throw new XmlValidationException('XML document is empty - missing root element');
    }

    return $document->documentElement;
  }

  /**
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlValidationException
   */
  private function getSchemaLocation(\DOMElement $root_element): string {
    $schema_location = $root_element->getAttributeNS(self::XML_SCHEMA_INSTANCE_NS, 'noNamespaceSchemaLocation');
    if ($schema_location === '') {
      throw new XmlValidationException('Missing XML schema location attribute');
    }

    return $schema_location;
  }

  private function resolveSchemaPath(string $file_path, string $schema_location): string {
    $base_dir = \dirname($file_path);
    return PathHelper::normalizePath("$base_dir/$schema_location");
  }

  /**
   * @throws \Drupal\niklan\ExternalContent\Exception\XmlValidationException
   */
  private function validateAgainstSchema(\DOMDocument $document, string $schema_path): void {
    \libxml_clear_errors();
    $previous_error_setting = \libxml_use_internal_errors(TRUE);

    try {
      if (!$document->schemaValidate($schema_path)) {
        throw new XmlValidationException("XML validation failed:\n" . $this->formatLibXmlErrors());
      }
    } finally {
      \libxml_use_internal_errors($previous_error_setting);
      \libxml_clear_errors();
    }
  }

  /**
   * @return string Formatted error messages
   */
  private function formatLibXmlErrors(): string {
    return \implode("\n", \array_map(
      fn (\LibXMLError $error): string => \sprintf(
        '[%s] Line %d: %s',
        $this->errorLevelToString($error->level),
        $error->line,
        \trim($error->message),
      ),
      \libxml_get_errors(),
    ));
  }

  private function errorLevelToString(int $level): string {
    return match ($level) {
      \LIBXML_ERR_WARNING => 'Warning',
      \LIBXML_ERR_ERROR => 'Error',
      \LIBXML_ERR_FATAL => 'Fatal Error',
      default => 'Unknown',
    };
  }

}
