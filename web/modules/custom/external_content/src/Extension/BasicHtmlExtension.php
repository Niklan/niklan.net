<?php declare(strict_types = 1);

namespace Drupal\external_content\Extension;

use Drupal\external_content\Builder\HtmlElementBuilder;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Data\PrioritizedList;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\external_content\Parser\Html\HtmlParser;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\external_content\Serializer\HtmlElementSerializer;
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
    $environment
      ->addParser(new HtmlParser())
      ->addBuilder(new HtmlElementBuilder())
      ->addSerializer(new HtmlElementSerializer())
      ->addSerializer(new PlainTextSerializer());
  }

  /**
   * {@inheritdoc}
   */
  public function configureSchema(ConfigurationBuilderInterface $builder): void {
    $parsers = new PrioritizedList();
    $parsers->add(new ElementParser(), -1000);
    $parsers->add(new PlainTextParser(), -900);

    // @todo Add default builders.
    $builders = new PrioritizedList();

    // @todo Add default normalizers.
    $normalizers = new PrioritizedList();

    $builder->addSchema('html', Expect::structure([
      'parsers' => Expect::type(PrioritizedList::class)
        ->required()
        ->default($parsers),
      'builders' => Expect::type(PrioritizedList::class)
        ->required()
        ->default($builders),
      'normalizers' => Expect::type(PrioritizedList::class)
        ->required()
        ->default($normalizers),
    ]));
  }

}
