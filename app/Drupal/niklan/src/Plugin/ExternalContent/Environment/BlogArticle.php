<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;
use Drupal\external_content\DataStructure\Nodes\RootNode;
use Drupal\external_content\Exporter\Array\ArrayExporter;
use Drupal\external_content\Exporter\Array\ArrayExporterContext;
use Drupal\external_content\Exporter\Array\ArrayExportRequest;
use Drupal\external_content\Exporter\Array\Builder\ArrayBuilder;
use Drupal\external_content\Exporter\Array\DefaultArrayBuilderExtension;
use Drupal\external_content\Importer\Array\ArrayImporter;
use Drupal\external_content\Importer\Array\ArrayImporterContext;
use Drupal\external_content\Importer\Array\ArrayImporterSource;
use Drupal\external_content\Importer\Array\ArrayImportRequest;
use Drupal\external_content\Importer\Array\DefaultArrayParserExtension;
use Drupal\external_content\Importer\Array\Parser\ArrayParser;
use Drupal\external_content\Importer\Html\DefaultHtmlParserExtension;
use Drupal\external_content\Importer\Html\HtmlImporter;
use Drupal\external_content\Importer\Html\HtmlImporterContext;
use Drupal\external_content\Importer\Html\HtmlImporterSource;
use Drupal\external_content\Importer\Html\HtmlImportRequest;
use Drupal\external_content\Importer\Html\Parser\HtmlParser;
use Drupal\external_content\Plugin\ExternalContent\Environment\Environment;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Importer\Array\RemoteVideoParser as RemoteVideoArrayParser;
use Drupal\niklan\ExternalContent\Importer\Html\RemoteVideoParser as RemoteVideoHtmlParser;
use Drupal\niklan\ExternalContent\Importer\Markdown\BlogArticleMarkdownSource;
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

  public function parse(ImporterSource $source): RootNode {
    \assert($source instanceof BlogArticleMarkdownSource);
    $html = $this->markdownConverter->convert($source->getSourceData());

    $parsers = new Registry();
    (new DefaultHtmlParserExtension())->register($parsers);
    $parsers->add(new RemoteVideoHtmlParser());

    $request = new HtmlImportRequest(
      new HtmlImporterSource($html->getContent()),
      new HtmlImporterContext($this->logger),
      new HtmlParser($parsers),
    );

    return (new HtmlImporter())->import($request);
  }

  public function denormalize(string $json): RootNode {
    $parsers = new Registry();
    (new DefaultArrayParserExtension())->register($parsers);
    $parsers->add(new RemoteVideoArrayParser());

    $request = new ArrayImportRequest(
      new ArrayImporterSource(\json_decode($json, TRUE)),
      new ArrayImporterContext($this->logger),
      new ArrayParser($parsers),
    );

    return (new ArrayImporter())->import($request);
  }

  public function normalize(RootNode $content): string {
    $builders = new Registry();
    (new DefaultArrayBuilderExtension())->register($builders);

    $request = new ArrayExportRequest(
      $content,
      new ArrayExporterContext($this->logger),
      new ArrayBuilder($builders),
    );

    return \json_encode((new ArrayExporter())->export($request)->toArray());
  }

  public function view(RootNode $content, ViewRequest $request): array {
    // TODO: Implement view() method.
  }

}
