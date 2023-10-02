<?php declare(strict_types = 1);

namespace Drupal\external_content\Field;

use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;

/**
 * Provides a computed field for "external_content" field type.
 *
 * @see \Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem
 */
final class ExternalContentComputedProperty extends TypedData {

  /**
   * {@selfdoc}
   */
  protected ?NodeInterface $value = NULL;

  /**
   * {@inheritdoc}
   */
  public function getValue(): ?NodeInterface {
    if ($this->value) {
      return $this->value;
    }

    $field_item = $this->getParent();
    \assert($field_item instanceof ExternalContentFieldItem);

    if ($field_item->validate()->count()) {
      return NULL;
    }

    $environment_plugin = self::getEnvironmentPluginManager()
      ->createInstance($field_item->get('environment_plugin_id')->getString());
    \assert($environment_plugin instanceof EnvironmentPluginInterface);

    $serializer = self::getSerializer();
    $serializer->setEnvironment($environment_plugin->getEnvironment());
    $element = $serializer->deserialize($field_item->get('value')->getValue());

    $this->value = $element;

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
