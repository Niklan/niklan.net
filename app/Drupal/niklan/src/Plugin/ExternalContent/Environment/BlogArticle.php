<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\Timer;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;
use Drupal\external_content\Exporter\Array\ArrayBuilder;
use Drupal\external_content\Exporter\Array\ArrayExporter;
use Drupal\external_content\Exporter\Array\ArrayExporterContext;
use Drupal\external_content\Exporter\Array\ArrayExportRequest;
use Drupal\external_content\Exporter\Array\ArrayExtension as DefaultArrayBuilderExtension;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuilder;
use Drupal\external_content\Exporter\RenderArray\RenderArrayExporter;
use Drupal\external_content\Exporter\RenderArray\RenderArrayExporterContext;
use Drupal\external_content\Exporter\RenderArray\RenderArrayExportRequest;
use Drupal\external_content\Exporter\RenderArray\RenderArrayExtension as DefaultRenderArrayExtension;
use Drupal\external_content\Importer\Array\ArrayExtension as DefaultArrayParserExtension;
use Drupal\external_content\Importer\Array\ArrayImporter;
use Drupal\external_content\Importer\Array\ArrayImporterContext;
use Drupal\external_content\Importer\Array\ArrayImporterSource;
use Drupal\external_content\Importer\Array\ArrayImportRequest;
use Drupal\external_content\Importer\Array\ArrayParser;
use Drupal\external_content\Importer\Html\HtmlExtension as DefaultHtmlParserExtension;
use Drupal\external_content\Importer\Html\HtmlImporter;
use Drupal\external_content\Importer\Html\HtmlImporterContext;
use Drupal\external_content\Importer\Html\HtmlImporterSource;
use Drupal\external_content\Importer\Html\HtmlImportRequest;
use Drupal\external_content\Importer\Html\HtmlParser;
use Drupal\external_content\Nodes\Root;
use Drupal\external_content\Plugin\ExternalContent\Environment\Environment;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Domain\MarkdownSource;
use Drupal\niklan\ExternalContent\Extension\ArrayBuilderExtension as CustomArrayBuilderExtension;
use Drupal\niklan\ExternalContent\Extension\ArrayParserExtension as CustomArrayParserExtension;
use Drupal\niklan\ExternalContent\Extension\HtmlParserExtension as CustomHtmlParserExtension;
use Drupal\niklan\ExternalContent\Extension\RenderArrayBuilderExtension as CustomRenderArrayExtension;
use League\CommonMark\MarkdownConverter;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  public function parse(ImporterSource $source): Root {
    \assert($source instanceof MarkdownSource);
    $html = $this->markdownConverter->convert($source->getSourceData());

    $parsers = new Registry();
    (new DefaultHtmlParserExtension())->register($parsers);
    (new CustomHtmlParserExtension())->register($parsers);

    $request = new HtmlImportRequest(
      source: new HtmlImporterSource($html->getContent()),
      context: new HtmlImporterContext($this->logger),
      htmlParser: new HtmlParser($parsers),
    );

    return (new HtmlImporter())->import($request);
  }

  public function denormalize(string $json): Root {
    $parsers = new Registry();
    (new DefaultArrayParserExtension())->register($parsers);
    (new CustomArrayParserExtension())->register($parsers);

    $request = new ArrayImportRequest(
      source: new ArrayImporterSource(\json_decode($json, TRUE)),
      context: new ArrayImporterContext($this->logger),
      parser: new ArrayParser($parsers),
    );

    return (new ArrayImporter())->import($request);
  }

  public function normalize(Root $content): string {
    $builders = new Registry();
    (new DefaultArrayBuilderExtension())->register($builders);
    (new CustomArrayBuilderExtension())->register($builders);

    $request = new ArrayExportRequest(
      content: $content,
      context: new ArrayExporterContext($this->logger),
      builder: new ArrayBuilder($builders),
    );

    return \json_encode((new ArrayExporter())->export($request)->toArray());
  }

  public function view(Root $content, ViewRequest $request): array {
    $builders = new Registry();
    (new DefaultRenderArrayExtension())->register($builders);
    (new CustomRenderArrayExtension())->register($builders);

    $request = new RenderArrayExportRequest(
      content: $content,
      context: new RenderArrayExporterContext($this->logger),
      builder: new RenderArrayBuilder($builders),
    );

    \dump($content);
    Timer::start('render-time');
    $result = (new RenderArrayExporter())->export($request)->toRenderArray();
    \dump(Timer::stop('render-time')['time']);
    return $result;
  }

}
