<?php declare(strict_types = 1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Builder\HtmlElementBuilder;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Parser\Html\HtmlParser;
use Drupal\external_content\Serializer\ExternalContentDocumentSerializer;
use Drupal\external_content\Serializer\HtmlElementSerializer;
use Drupal\external_content\Serializer\PlainTextSerializer;

/**
 * Provides a very basic extension with most useful settings.
 */
final class BasicHtmlExtension implements ExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment
      // Prioritize it over everything, it's a special element.
      ->addSerializer(new ExternalContentDocumentSerializer(), 1_000)
      // The basic HTML element.
      ->addParser(new HtmlParser())
      ->addBuilder(new HtmlElementBuilder())
      ->addSerializer(new HtmlElementSerializer())
      ->addSerializer(new PlainTextSerializer());
  }

}
