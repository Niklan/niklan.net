<?php

declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\external_content\Builder\ChainRenderArrayBuilderInterface;
use Drupal\external_content\Plugin\Field\FieldType\ParsedSourceFileItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides field formatter for parsed source file fields.
 *
 * @FieldFormatter(
 *   id = "external_content_rendered_parsed_source_file",
 *   label = @Translation("Rendered parsed source file"),
 *   field_types = {
 *     "external_content_parsed_source_file",
 *   }
 * )
 */
final class RenderedParsedSourceFileFormatter extends FormatterBase {

  /**
   * The chained render array builder.
   */
  protected ChainRenderArrayBuilderInterface $chainRenderArrayBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition,
    );

    $instance->setChainRenderArrayBuilder(
      $container->get(ChainRenderArrayBuilderInterface::class),
    );

    return $instance;
  }

  /**
   * Sets chained render array builder.
   *
   * @param \Drupal\external_content\Builder\ChainRenderArrayBuilderInterface $builder
   *   The builder.
   *
   * @return $this
   */
  public function setChainRenderArrayBuilder(ChainRenderArrayBuilderInterface $builder): self {
    $this->chainRenderArrayBuilder = $builder;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      \assert($item instanceof ParsedSourceFileItem);
      $parsed_source_file = $item->getParsedSourceFile();
      $content = $parsed_source_file->getContent();
      $elements[$delta] = $this->chainRenderArrayBuilder->build($content);
    }

    return $elements;
  }

}
