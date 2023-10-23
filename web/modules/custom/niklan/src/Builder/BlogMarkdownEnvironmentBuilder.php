<?php declare(strict_types = 1);

namespace Drupal\niklan\Builder;

use Drupal\niklan\CommonMark\Adapter\EmbedAdapter;
use Drupal\niklan\CommonMark\Extension\NiklanMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 * @ingroup markdown
 */
final class BlogMarkdownEnvironmentBuilder {

  /**
   * {@selfdoc}
   */
  private ?EnvironmentInterface $environment = NULL;

  /**
   * {@selfdoc}
   */
  public function build(): EnvironmentInterface {
    if ($this->environment) {
      return $this->environment;
    }

    $configuration = [
      'embed' => [
        'adapter' => new EmbedAdapter(),
      ],
    ];

    $this->environment = new Environment($configuration);
    $this->environment->addExtension(new NiklanMarkdownExtension());

    return $this->environment;
  }

}
