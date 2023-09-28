<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderFacadeInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentDocumentItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'With icon' formatter.
 *
 * @FieldFormatter(
 *   id = "external_content_render_array",
 *   label = @Translation("Render array builder"),
 *   field_types = {
 *     "external_content_document"
 *   }
 * )
 */
final class ExternalContentDocumentRenderArrayFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

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
    private readonly RenderArrayBuilderFacadeInterface $renderArrayBuilder,
    private readonly EnvironmentPluginManagerInterface $environmentPluginManager,
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
      $container->get(RenderArrayBuilderFacadeInterface::class),
      $container->get(EnvironmentPluginManagerInterface::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $item) {
      \assert($item instanceof ExternalContentDocumentItem);
      $document = $item->get('document')->getValue();
      $is_valid_environment_plugin = $item->get('environment_plugin_id')->validate()->count() === 0;

      if (!($document instanceof ExternalContentDocument) || !$is_valid_environment_plugin) {
        continue;
      }

      $environment_plugin_id = $item->get('environment_plugin_id')->getValue();
      $environment_plugin = $this
        ->environmentPluginManager
        ->createInstance($environment_plugin_id);
      \assert($environment_plugin instanceof EnvironmentPluginInterface);

      $this
        ->renderArrayBuilder
        ->setEnvironment($environment_plugin->getEnvironment());

      $build = $this->renderArrayBuilder->build($document);
      $element[] = $build->getRenderArray();
    }

    return $element;
  }

}
