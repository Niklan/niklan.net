<?php declare(strict_types = 1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Builder\Html\ElementRenderArrayBuilder;
use Drupal\external_content\Builder\Html\PlainTextRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Data\PrioritizedList;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\external_content\Parser\Html\HtmlParser;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\external_content\Serializer\ElementSerializer;
use Drupal\external_content\Serializer\PlainTextSerializer;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

/**
 * Provides a very basic extension with most useful settings.
 */
final class BasicHtmlExtension implements ConfigurableExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    // Add default parsers if none provided.
    // @todo COmplete it. Returns NULL with an empty value.
    if (!$environment->getConfiguration()->exists('html.parsers')) {
      $parsers = new PrioritizedList();
      $parsers->add(new ElementParser(), -1000);
      $parsers->add(new PlainTextParser(), -900);

      $environment
        ->getConfiguration()
        ->set('html.parsers', $parsers);
    }

    $environment
      ->addParser(new HtmlParser())
      ->addBuilder(new ElementRenderArrayBuilder())
      ->addBuilder(new PlainTextRenderArrayBuilder())
      ->addSerializer(new ElementSerializer())
      ->addSerializer(new PlainTextSerializer());
  }

  /**
   * {@inheritdoc}
   */
  public function configureSchema(ConfigurationBuilderInterface $builder): void {
    $builder->addSchema('html', Expect::structure([
      'parsers' => Expect::type(PrioritizedList::class),
    ]));
  }

}
