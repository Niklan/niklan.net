<?php

declare(strict_types=1);

namespace Drupal\external_content\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Provides a computed field for "external_content" field type.
 *
 * @see \Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem
 */
final class ExternalContentComputedProperty extends TypedData {

  protected ?NodeInterface $value = NULL;

  #[\Override]
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
      $environment = self::getExternalContentManager()
        ->getEnvironmentManager()
        ->get(environment_id: $environment_id);
    }
    catch (\Exception) {
      return NULL;
    }

    $json = $field_item->get('value')->getValue();
    \assert(\is_string($json));

    $this->value = self::getExternalContentManager()->getSerializerManager()->deserialize($json, $environment);

    return $this->value;
  }

  private static function getExternalContentManager(): ExternalContentManagerInterface {
    return \Drupal::service(ExternalContentManagerInterface::class);
  }

}
