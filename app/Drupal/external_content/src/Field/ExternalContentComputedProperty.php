<?php

declare(strict_types=1);

namespace Drupal\external_content\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentPluginManager;

/**
 * @see \Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem
 */
final class ExternalContentComputedProperty extends TypedData {

  protected ?ContentNode $value = NULL;

  #[\Override]
  public function getValue(): ?ContentNode {
    if ($this->value) {
      return $this->value;
    }

    $field_item = $this->getParent();
    \assert($field_item instanceof FieldItemInterface);
    if ($field_item->validate()->count()) {
      return NULL;
    }

    $environment_id = $field_item->get('environment_id')->getValue();
    $raw_value = $field_item->get('value')->getValue();

    $environment_plugin = self::getEnvironmentPluginManager()->createInstance($environment_id);
    $this->value = $environment_plugin->denormalize($raw_value);

    return $this->value;
  }

  private static function getEnvironmentPluginManager(): EnvironmentPluginManager {
    return \Drupal::service(EnvironmentPluginManager::class);
  }

}
