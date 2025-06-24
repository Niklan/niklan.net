<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Contract\Importer\ContentImporterSource;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;
use Drupal\external_content\Exporter\Array\Builder;
use Drupal\external_content\Exporter\Array\Exporter;
use Drupal\external_content\Exporter\Array\ExporterContext;
use Drupal\external_content\Exporter\Array\ExportRequest;
use Drupal\external_content\Exporter\Array\DefaultExtension;
use Drupal\external_content\Importer\Array\ArrayContentImporter;
use Drupal\external_content\Importer\Array\ArrayContentImporterContext;
use Drupal\external_content\Importer\Array\ArrayContentImporterSource;
use Drupal\external_content\Importer\Array\ArrayContentImportRequest;
use Drupal\external_content\Importer\Array\ArrayParser;
use Drupal\external_content\Importer\Array\DefaultArrayParserExtension;
use Drupal\external_content\Importer\Html\DefaultHtmlParserExtension;
use Drupal\external_content\Importer\Html\HtmlContentImporter;
use Drupal\external_content\Importer\Html\HtmlContentImporterContext;
use Drupal\external_content\Importer\Html\HtmlContentImporterSource;
use Drupal\external_content\Importer\Html\HtmlContentImportRequest;
use Drupal\external_content\Importer\Html\HtmlParser;
use Drupal\external_content\Nodes\RootNode;
use Drupal\external_content\Plugin\ExternalContent\Environment\Environment;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Domain\MarkdownSourceContent;
use Drupal\niklan\ExternalContent\Extension\ArrayParserExtension;
use Drupal\niklan\ExternalContent\Extension\HtmlParserExtension;
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

  public function parse(ContentImporterSource $source): RootNode {
    \assert($source instanceof MarkdownSourceContent);
    $html = $this->markdownConverter->convert($source->getSourceData());

    $parsers = new Registry();
    (new DefaultHtmlParserExtension())->register($parsers);
    (new HtmlParserExtension())->register($parsers);

    $request = new HtmlContentImportRequest(
      new HtmlContentImporterSource($html->getContent()),
      new HtmlContentImporterContext($this->logger),
      new HtmlParser($parsers),
    );

    return (new HtmlContentImporter())->import($request);
  }

  public function denormalize(string $json): RootNode {
    $parsers = new Registry();
    (new DefaultArrayParserExtension())->register($parsers);
    (new ArrayParserExtension())->register($parsers);

    $request = new ArrayContentImportRequest(
      new ArrayContentImporterSource(\json_decode($json, TRUE)),
      new ArrayContentImporterContext($this->logger),
      new ArrayParser($parsers),
    );

    return (new ArrayContentImporter())->import($request);
  }

  public function normalize(RootNode $content): string {
    $builders = new Registry();
    (new DefaultExtension())->register($builders);

    $request = new ExportRequest(
      $content,
      new ExporterContext($this->logger),
      new Builder($builders),
    );

    return \json_encode((new Exporter())->export($request)->toArray());
  }

  public function view(RootNode $content, ViewRequest $request): array {
    return [
      '#markup' => 'TODO',
    ];
  }

}
