<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
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
  public static function defaultSettings(): array {
    return [
      'environment' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['environment'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => new TranslatableMarkup('Environment'),
      '#description' => new TranslatableMarkup('The external content environment that will be used for rendering content.'),
      '#default_value' => $this->getSetting('environment'),
      '#options' => $this->getEnvironmentPluginOptions(),
    ];

    return $form;
  }

  /**
   * {@selfdoc}
   */
  private function getEnvironmentPluginOptions(): array {
    return \array_map(
      static fn (array $definition) => "{$definition['label']} ({$definition['id']})",
      $this->environmentPluginManager->getDefinitions(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    if ($this->getSetting('environment')) {
      $summary[] = (string) new TranslatableMarkup('Environment: @environment', [
        '@environment' => $this->getEnvironmentPluginOptions()[$this->configuration['environment']],
      ]);
    }
    else {
      $summary[] = (string) new TranslatableMarkup('Environment: @environment', [
        '@environment' => new TranslatableMarkup('none'),
      ]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $environment_plugin_id = $this->getSetting('environment');

    if (!$environment_plugin_id) {
      return [];
    }

    if (!$this->environmentPluginManager->hasDefinition($environment_plugin_id)) {
      return [];
    }

    $environment_plugin = $this
      ->environmentPluginManager
      ->createInstance($this->getSetting('environment'));

    \dump($environment_plugin);

    if (!$environment_plugin instanceof EnvironmentPluginInterface) {
      return [];
    }

    $this
      ->renderArrayBuilder
      ->setEnvironment($environment_plugin->getEnvironment());

    $element = [];

    foreach ($items as $item) {
      \assert($item instanceof ExternalContentDocumentItem);
      $document = $item->get('document')->getValue();

      if (!$document instanceof ExternalContentDocument) {
        continue;
      }

      $element[] = $this->renderArrayBuilder->build($document);
    }

    return $element;
  }

}
