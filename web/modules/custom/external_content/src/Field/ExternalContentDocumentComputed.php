<?php declare(strict_types = 1);

namespace Drupal\external_content\Field;

use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentDocumentItem;

/**
 * Provides a computed field for "external_content_document" field type.
 *
 * @see \Drupal\external_content\Plugin\Field\FieldType\ExternalContentDocumentItem
 */
final class ExternalContentDocumentComputed extends TypedData {

  /**
   * {@selfdoc}
   */
  protected ?ExternalContentDocument $value = NULL;

  /**
   * {@inheritdoc}
   */
  public function getValue(): ?ExternalContentDocument {
    if ($this->value) {
      return $this->value;
    }

    $field_item = $this->getParent();
    \assert($field_item instanceof ExternalContentDocumentItem);

    if ($field_item->get('value')->validate()->count() > 0) {
      return NULL;
    }

    $json = $field_item->get('value')->getValue();
    $serializer = self::getSerializer();
    // @todo Store environment plugin ID with a field value and set it here
    //   for serializer.
    \assert($element instanceof ExternalContentDocument);
    $this->value = $element;

    return $this->value;
  }

  /**
   * {@selfdoc}
   */
  private static function getSerializer(): SerializerInterface {
    return \Drupal::service(SerializerInterface::class);
  }

}
