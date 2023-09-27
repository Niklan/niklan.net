<?php declare(strict_types = 1);

namespace Drupal\external_content\Field;

use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
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

    if ($field_item->get('environment_plugin_id')->validate()->count() > 0) {
      return NULL;
    }

    $environment_plugin = self::getEnvironmentPluginManager()
      ->createInstance($field_item->get('environment_plugin_id')->getString());
    \assert($environment_plugin instanceof EnvironmentPluginInterface);

    $serializer = self::getSerializer();
    $serializer->setEnvironment($environment_plugin->getEnvironment());
    $document = $serializer->deserialize($field_item->get('value')->getValue());

    if (!($document instanceof ExternalContentDocument)) {
      return NULL;
    }

    $this->value = $document;

    return $this->value;
  }

  /**
   * {@selfdoc}
   */
  private static function getSerializer(): SerializerInterface {
    return \Drupal::service(SerializerInterface::class);
  }

  /**
   * {@selfdoc}
   */
  private static function getEnvironmentPluginManager(): EnvironmentPluginManagerInterface {
    return \Drupal::service(EnvironmentPluginManagerInterface::class);
  }

}
