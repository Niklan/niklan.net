<?php declare(strict_types = 1);

namespace Drupal\external_content\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;

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
    \assert($field_item instanceof FieldItemInterface);

    if ($field_item->validate()->count()) {
      return NULL;
    }

    $environment_id = $field_item->get('environment_id')->getString();

    try {
      $environment = self::getEnvironmentManager()->getEnvironment(
        environment_id: $environment_id,
      );
    }
    catch (\Exception) {
      return NULL;
    }

    $serializer = self::getSerializer();
    $serializer->setEnvironment($environment);
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
  private static function getEnvironmentManager(): EnvironmentManagerInterface {
    return \Drupal::service(EnvironmentManagerInterface::class);
  }

}
