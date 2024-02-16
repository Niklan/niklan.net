<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Environment;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\PrioritizedList;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Event\FileFoundEvent;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Extension\FileFinderExtension;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentPlugin;
use Drupal\niklan\Builder\ExternalContent\RenderArray\CodeBlock;
use Drupal\niklan\Builder\ExternalContent\RenderArray\DrupalMedia;
use Drupal\niklan\Builder\ExternalContent\RenderArray\LinkBuilder;
use Drupal\niklan\Builder\ExternalContent\RenderArray\Note;
use Drupal\niklan\Converter\BlogMarkdownConverter;
use Drupal\niklan\Identifier\ExternalContent\FrontMatterIdentifier;
use Drupal\niklan\Loader\ExternalContent\Blog;
use Drupal\niklan\Parser\ExternalContent\NoteParser;
use Drupal\niklan\Serializer\ExternalContent\DrupalMediaSerializer;
use Drupal\niklan\Serializer\ExternalContent\NoteSerializer;
use League\Config\Configuration;
use Nette\Schema\Expect;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 *
 * @ExternalContentEnvironment(
 *   id = "blog",
 *   label = @Translation("Field item environment"),
 *  )
 */
final class BlogEnvironment extends EnvironmentPlugin implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new BlogEnvironment instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private ContainerInterface $container,
    private BlogMarkdownConverter $markdownConverter,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container,
      $container->get(BlogMarkdownConverter::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    $configuration = new Configuration();
    $configuration->set('html.parsers', $this->prepareHtmlParsers());
    $configuration->set('html.supported_types', ['text/markdown']);
    $configuration->set('file_finder.extensions', ['md']);
    $configuration->set('file_finder.directories', ['private://content']);

    $configuration->addSchema('environment_plugin_id', Expect::string());
    $configuration->set('environment_plugin_id', $this->getPluginId());

    $environment = new Environment($configuration);
    $environment->setContainer($this->container);
    $environment->addIdentifier(new FrontMatterIdentifier());
    $environment->addExtension(new BasicHtmlExtension());
    $environment->addExtension(new FileFinderExtension());
    // Drupal media handler.
    $environment->addSerializer(new DrupalMediaSerializer());
    $environment->addBuilder(new DrupalMedia());
    // Code block custom builder.
    $environment->addBuilder(new CodeBlock(), 100);
    // Note syntax support.
    $environment->addBuilder(new Note(), 100);
    $environment->addSerializer(new NoteSerializer());
    // Blog entry loader.
    $environment->addLoader(new Blog());
    $environment->addBuilder(new LinkBuilder(), 100);

    $environment->addEventListener(
      event_class: FileFoundEvent::class,
      listener: fn (FileFoundEvent $event) => $this->extractSourceFrontMatter($event),
    );
    $environment->addEventListener(
      event_class: HtmlPreParseEvent::class,
      listener: fn (HtmlPreParseEvent $event) => $this->removeFrontMatter($event),
    );
    $environment->addEventListener(
      event_class: HtmlPreParseEvent::class,
      listener: fn (HtmlPreParseEvent $event) => $this->convertMarkdown($event),
    );

    return $environment;
  }

  /**
   * {@selfdoc}
   */
  private function extractSourceFrontMatter(FileFoundEvent $event): void {
    $front_matter = FrontMatter::create($event->file->contents());
    $event->file->data()->set('front_matter', $front_matter->getData());
  }

  /**
   * {@selfdoc}
   */
  private function removeFrontMatter(HtmlPreParseEvent $event): void {
    $front_matter = FrontMatter::create($event->content);
    // Replace content with the version without Front Matter.
    $event->content = $front_matter->getContent();
  }

  /**
   * {@selfdoc}
   */
  private function convertMarkdown(HtmlPreParseEvent $event): void {
    $html = $this->markdownConverter->convert($event->content);
    $event->content = $html;
  }

  /**
   * {@selfdoc}
   */
  private function prepareHtmlParsers(): PrioritizedList {
    $parsers = new PrioritizedList();
    $parsers->add(new NoteParser(), 100);
    $parsers->add(new PlainTextParser(), 0);
    $parsers->add(new ElementParser(), -100);

    return $parsers;
  }

}
