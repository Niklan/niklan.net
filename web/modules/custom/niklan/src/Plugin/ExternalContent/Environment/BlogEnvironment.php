<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Environment;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\PrioritizedList;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Event\HtmlPreParseEvent;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Parser\Html\ElementParser;
use Drupal\external_content\Parser\Html\PlainTextParser;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentPlugin;
use Drupal\niklan\Converter\BlogMarkdownConverter;
use League\Config\Configuration;
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
      $container->get(BlogMarkdownConverter::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    $configuration = new Configuration();
    $configuration->set('html.parsers', $this->prepareHtmlParsers());

    $environment = new Environment($configuration);
    $environment->addExtension(new BasicHtmlExtension());

    $environment->addEventListener(
      event_class: HtmlPreParseEvent::class,
      listener: fn (HtmlPreParseEvent $event) => $this->extractFrontMatter($event),
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
  private function extractFrontMatter(HtmlPreParseEvent $event): void {
    $front_matter = FrontMatter::create($event->content);
    $event->data->set('front_matter', $front_matter->getData());
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
    $parsers->add(new PlainTextParser(), 0);
    $parsers->add(new ElementParser(), -1000);

    return $parsers;
  }

}
