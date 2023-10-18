<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\niklan\Finder\MarkdownFinder;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
  ) {}

  /**
   * {@selfdoc}
   */
  public function build(): EnvironmentInterface {
    if ($this->environment) {
      return $this->environment;
    }

    $configuration = new Configuration([
      'markdown_finder' => [
        // @todo Replace by state value.
        'dirs' => 'private://content',
      ],
    ]);

    $this->environment = new Environment($configuration);
    $this->environment->setContainer($this->container);
    $this->environment->addExtension(new BasicHtmlExtension());
    $this->environment->addFinder(new MarkdownFinder());

    return $this->environment;
  }

}
