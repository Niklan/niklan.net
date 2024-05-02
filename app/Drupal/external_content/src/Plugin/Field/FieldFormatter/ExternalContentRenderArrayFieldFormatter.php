<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 *
 * @FieldFormatter(
 *   id = "external_content_render_array",
 *   label = @Translation("Render array builder"),
 *   field_types = {
 *     "external_content"
 *   }
 * )
 */
final class ExternalContentRenderArrayFieldFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    private readonly ExternalContentManagerInterface $externalContentManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get(ExternalContentManagerInterface::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $item) {
      \assert($item instanceof ExternalContentFieldItem);

      if ($item->validate()->count()) {
        continue;
      }

      $content = $item->get('content')->getValue();
      \assert($content instanceof NodeInterface);

      $environment_id = $item->get('environment_id')->getValue();

      if (!$this->externalContentManager->getEnvironmentManager()->has($environment_id)) {
        continue;
      }

      $environment = $this
        ->externalContentManager
        ->getEnvironmentManager()
        ->get($environment_id);

      $element[] = $this
        ->externalContentManager
        ->getRenderArrayBuilderManager()
        ->build(node: $content, environment: $environment)
        ->result();
    }

    return $element;
  }

}
