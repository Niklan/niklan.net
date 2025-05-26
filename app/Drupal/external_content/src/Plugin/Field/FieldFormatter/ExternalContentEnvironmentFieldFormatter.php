<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;
use Drupal\external_content\DataStructure\Nodes\RootNode;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[FieldFormatter(
  id: self::ID,
  label: new TranslatableMarkup('Environment-based render array builder'),
  field_types: [
    ExternalContentFieldItem::ID,
  ],
)]
final class ExternalContentEnvironmentFieldFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  public const string ID = 'external_content_environment';

  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    private readonly EnvironmentManager $environmentPluginManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get(EnvironmentManager::class),
    );
  }

  #[\Override]
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $item) {
      \assert($item instanceof ExternalContentFieldItem);
      if ($item->validate()->count()) {
        continue;
      }

      $content = $item->get('content')->getValue();
      \assert($content instanceof RootNode);
      $environment_id = $item->get('environment_id')->getValue();
      \assert(\is_string($environment_id));
      $environment = $this->environmentPluginManager->createInstance($environment_id);
      \assert($environment instanceof EnvironmentPlugin);

      $request = new ViewRequest($item->getEntity(), $this->viewMode);
      $element[] = $environment->view($content, $request);
    }

    return $element;
  }

}
