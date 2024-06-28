<?php

declare(strict_types=1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;

/**
 * Provides a very basic extension with most useful settings.
 *
 * @todo Consider creating dedicated extensions for plain text, code and
 *   element, and use them here.
 */
final class BasicHtmlExtension implements ExtensionInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ExternalContentManagerInterface $externalContentManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    $html_parser_manager = $this
      ->externalContentManager
      ->getHtmlParserManager();
    $serializer_manager = $this->externalContentManager->getSerializerManager();
    $render_array_builder_manager = $this
      ->externalContentManager
      ->getRenderArrayBuilderManager();

    $environment
      ->addHtmlParser($html_parser_manager->get('plain_text'))
      ->addHtmlParser($html_parser_manager->get('code'), -40)
      ->addHtmlParser($html_parser_manager->get('element'), -50)
      ->addRenderArrayBuilder($render_array_builder_manager->get('plain_text'), -50)
      ->addRenderArrayBuilder($render_array_builder_manager->get('element'), -50)
      ->addRenderArrayBuilder($render_array_builder_manager->get('code'), -50)
      ->addRenderArrayBuilder($render_array_builder_manager->get('content'), -50)
      ->addSerializer($serializer_manager->get('element'), -50)
      ->addSerializer($serializer_manager->get('plain_text'), -50)
      ->addSerializer($serializer_manager->get('code'), -50)
      ->addSerializer($serializer_manager->get('content'), -50);
  }

}
