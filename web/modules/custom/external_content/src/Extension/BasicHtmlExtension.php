<?php declare(strict_types = 1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Builder\HtmlElementBuilder;
use Drupal\external_content\Builder\PlainTextBuilder;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Parser\HtmlElementParser;
use Drupal\external_content\Parser\PlainTextParser;
use Drupal\external_content\Serializer\ExternalContentDocumentSerializer;
use Drupal\external_content\Serializer\HtmlElementSerializer;
use Drupal\external_content\Serializer\PlainTextSerializer;

/**
 * Provides a very basic extension with most useful settings.
 */
final class BasicHtmlExtension implements ExtensionInterface {

  /**
   * {@selfdoc}
   */
  private const WEIGHT = -1_000;

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment
      // Prioritize it over everything, it's a special element.
      ->addSerializer(new ExternalContentDocumentSerializer(), 1_000)
      // The basic HTML element.
      ->addParser(new HtmlElementParser(), self::WEIGHT)
      ->addBuilder(new HtmlElementBuilder(), self::WEIGHT)
      ->addSerializer(new HtmlElementSerializer(), self::WEIGHT)
      // The plain text.
      ->addParser(new PlainTextParser(), self::WEIGHT)
      ->addBuilder(new PlainTextBuilder(), self::WEIGHT)
      ->addSerializer(new PlainTextSerializer(), self::WEIGHT);
  }

}
