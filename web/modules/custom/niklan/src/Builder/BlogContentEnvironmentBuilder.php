<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\niklan\Bundler\FrontMatterIdLanguageBundler;
use Drupal\niklan\Finder\MarkdownFinder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class BlogContentEnvironmentBuilder {

  /**
   * {@selfdoc}
   */
  private ?EnvironmentInterface $environment = NULL;

  /**
   * Constructs a new BlogContentEnvironmentBuilder instance.
   */
  public function __construct(
    private ContainerInterface $container,
    private EventDispatcherInterface $eventDispatcher,
  ) {}

  /**
   * {@selfdoc}
   */
  public function build(string $content_directory): EnvironmentInterface {
    if ($this->environment) {
      return $this->environment;
    }

    $configuration = new Configuration([
      'markdown_finder' => [
        'dirs' => $content_directory,
      ],
    ]);
    $this->environment = new Environment($configuration);

    $this->environment->setContainer($this->container);
    $this->environment->setEventDispatcher($this->eventDispatcher);

    $this->environment->addExtension(new BasicHtmlExtension());
    $this->environment->addFinder(new MarkdownFinder());
    $this->environment->addBundler(new FrontMatterIdLanguageBundler());

    return $this->environment;
  }

}
