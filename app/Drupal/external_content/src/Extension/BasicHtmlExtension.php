<?php declare(strict_types = 1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Builder\Html\ElementRenderArrayRenderArrayBuilder;
use Drupal\external_content\Builder\Html\PlainTextRenderArrayRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;

/**
 * Provides a very basic extension with most useful settings.
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

    $environment
      ->addHtmlParser($html_parser_manager->get('plain_text'), 50)
      ->addHtmlParser($html_parser_manager->get('element'))
      ->addRenderArrayBuilder(new ElementRenderArrayRenderArrayBuilder())
      ->addRenderArrayBuilder(new PlainTextRenderArrayRenderArrayBuilder())
      ->addSerializer($serializer_manager->get('element'))
      ->addSerializer($serializer_manager->get('plain_text'))
      ->addSerializer($serializer_manager->get('content'));
  }

}
