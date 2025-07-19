<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Builder\Array\ArrayBuilder;
use Drupal\external_content\Builder\Array\ArrayExtension as DefaultArrayBuilderExtension;
use Drupal\external_content\Builder\RenderArray\RenderArrayBuilder;
use Drupal\external_content\Builder\RenderArray\RenderArrayExtension as DefaultRenderArrayExtension;
use Drupal\external_content\Contract\Extension\ExtensionManager;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Parser\Array\ArrayExtension as DefaultArrayParserExtension;
use Drupal\external_content\Parser\Array\ArrayParser;
use Drupal\external_content\Parser\Html\HtmlExtension as DefaultHtmlParserExtension;
use Drupal\external_content\Parser\Html\HtmlParser;
use Drupal\external_content\Plugin\ExternalContent\Environment\Environment;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Extension\ArrayBuilderExtension as CustomArrayBuilderExtension;
use Drupal\niklan\ExternalContent\Extension\ArrayParserExtension as CustomArrayParserExtension;
use Drupal\niklan\ExternalContent\Extension\HtmlParserExtension as CustomHtmlParserExtension;
use Drupal\niklan\ExternalContent\Extension\RenderArrayBuilderExtension as CustomRenderArrayExtension;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @implements \Drupal\external_content\Contract\Plugin\EnvironmentPlugin<string>
 */
#[Environment(
  id: self::ID,
  label: new TranslatableMarkup('Blog article'),
)]
final class BlogArticle extends PluginBase implements EnvironmentPlugin, ContainerFactoryPluginInterface {

  public const string ID = 'niklan_blog_article';

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly MarkdownConverter $markdownConverter,
    private readonly ExtensionManager $extensionManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration, $plugin_id, $plugin_definition,
      $container->get(MarkdownConverter::class),
      $container->get(ExtensionManager::class),
    );
  }

  public function parse(mixed $source): Document {
    $parsers = new Registry();
    $this->extensionManager->get(DefaultHtmlParserExtension::ID)->register($parsers);
    $this->extensionManager->get(CustomHtmlParserExtension::ID)->register($parsers);

    // @phpstan-ignore-next-line argument.type
    return (new HtmlParser($parsers))->parse($this->markdownConverter->convert($source)->getContent());
  }

  public function denormalize(string $json): Document {
    $parsers = new Registry();
    $this->extensionManager->get(DefaultArrayParserExtension::ID)->register($parsers);
    $this->extensionManager->get(CustomArrayParserExtension::ID)->register($parsers);

    $array = \json_decode($json, TRUE);
    \assert(\is_array($array));

    // @phpstan-ignore-next-line argument.type
    return (new ArrayParser($parsers))->parse(ArrayElement::fromArray($array));
  }

  public function normalize(Document $content): string {
    $builders = new Registry();
    $this->extensionManager->get(DefaultArrayBuilderExtension::ID)->register($builders);
    $this->extensionManager->get(CustomArrayBuilderExtension::ID)->register($builders);

    // @phpstan-ignore-next-line argument.type
    $json = \json_encode((new ArrayBuilder($builders))->build($content)->toArray());
    \assert(\is_string($json));

    return $json;
  }

  public function view(Document $content, ViewRequest $request): array {
    $builders = new Registry();
    $this->extensionManager->get(DefaultRenderArrayExtension::ID)->register($builders);
    $this->extensionManager->get(CustomRenderArrayExtension::ID)->register($builders);

    // @phpstan-ignore-next-line argument.type
    return (new RenderArrayBuilder($builders))->build($content)->toRenderArray();
  }

}
