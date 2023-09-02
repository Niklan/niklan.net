<?php declare(strict_types = 1);

namespace Drupal\external_content\Field;

use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentDocumentItem;
use Drupal\external_content\Serializer\Serializer;

/**
 * Provides a computed field for "external_content_document" field type.
 *
 * @see \Drupal\external_content\Plugin\Field\FieldType\ExternalContentDocumentItem
 */
final class ExternalContentDocumentComputed extends TypedData {

  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   */
  protected function computeValue(): void {
    $field_item = $this->getParent();
    \assert($field_item instanceof ExternalContentDocumentItem);

    if ($field_item->get('value')->validate()->count() > 0) {
      $this->setValue(NULL);

      return;
    }

    $serializer = new Serializer();
    $json = $field_item->get('value')->getValue();
    $document = $serializer->deserialize($json);

    $this->setValue($document);
  }

}
