<?php declare(strict_types = 1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Builder\Html\ElementRenderArrayBuilder;
use Drupal\external_content\Builder\Html\PlainTextRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Serializer\ContentSerializer;
use Drupal\external_content\Serializer\ElementSerializer;
use Drupal\external_content\Serializer\PlainTextSerializer;

/**
 * Provides a very basic extension with most useful settings.
 */
final class BasicHtmlExtension {

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

    $environment
      ->addHtmlParser($html_parser_manager->get('plain_text'), 50)
      ->addHtmlParser($html_parser_manager->get('element'))
      ->addBuilder(new ElementRenderArrayBuilder())
      ->addBuilder(new PlainTextRenderArrayBuilder())
      ->addSerializer(new ElementSerializer())
      ->addSerializer(new PlainTextSerializer())
      ->addSerializer(new ContentSerializer());
  }

}
