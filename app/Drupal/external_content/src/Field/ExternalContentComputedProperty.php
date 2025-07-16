<?php

declare(strict_types=1);

namespace Drupal\external_content\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\TypedData\TypedData;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;

/**
 * @see \Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem
 */
final class ExternalContentComputedProperty extends TypedData {

  protected ?Node $value = NULL;

  #[\Override]
  public function getValue(): ?Node {
    if ($this->value) {
      return $this->value;
    }

    $field_item = $this->getParent();
    \assert($field_item instanceof FieldItemInterface);
    if ($field_item->validate()->count()) {
      return NULL;
    }

    $environment_id = $field_item->get('environment_id')->getValue();
    \assert(\is_string($environment_id));
    $raw_value = $field_item->get('value')->getValue();

    $environment_plugin = self::getEnvironmentPluginManager()->createInstance($environment_id);
    $this->value = $environment_plugin->denormalize($raw_value);

    return $this->value;
  }

  private static function getEnvironmentPluginManager(): EnvironmentManager {
    return \Drupal::service(EnvironmentManager::class);
  }

}
