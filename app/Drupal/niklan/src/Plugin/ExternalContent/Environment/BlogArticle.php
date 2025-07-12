<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\Timer;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Builder\Array\ArrayBuilder;
use Drupal\external_content\Builder\Array\ArrayExtension as DefaultArrayBuilderExtension;
use Drupal\external_content\Builder\RenderArray\RenderArrayBuilder;
use Drupal\external_content\Builder\RenderArray\RenderArrayExtension as DefaultRenderArrayExtension;
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
use Psr\Log\LoggerInterface;
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
    private readonly LoggerInterface $logger,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration, $plugin_id, $plugin_definition,
      $container->get(MarkdownConverter::class),
      $container->get('logger.channel.niklan.external_content'),
    );
  }

  public function parse(mixed $source): Document {
    $parsers = new Registry();
    (new DefaultHtmlParserExtension())->register($parsers);
    (new CustomHtmlParserExtension())->register($parsers);

    return (new HtmlParser($parsers))->parse($this->markdownConverter->convert($source)->getContent());
  }

  public function denormalize(string $json): Document {
    $parsers = new Registry();
    (new DefaultArrayParserExtension())->register($parsers);
    (new CustomArrayParserExtension())->register($parsers);

    return (new ArrayParser($parsers))->parse(ArrayElement::fromArray(\json_decode($json, TRUE)));
  }

  public function normalize(Document $content): string {
    $builders = new Registry();
    (new DefaultArrayBuilderExtension())->register($builders);
    (new CustomArrayBuilderExtension())->register($builders);

    return \json_encode((new ArrayBuilder($builders))->build($content)->toArray());
  }

  public function view(Document $content, ViewRequest $request): array {
    $builders = new Registry();
    (new DefaultRenderArrayExtension())->register($builders);
    (new CustomRenderArrayExtension())->register($builders);

    \dump($content);
    Timer::start('render-time');
    $result = (new RenderArrayBuilder($builders))->build($content)->toRenderArray();
    \dump(Timer::stop('render-time')['time']);
    return $result;
  }

}
