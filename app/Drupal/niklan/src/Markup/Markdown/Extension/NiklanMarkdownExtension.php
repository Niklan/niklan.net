<?php

declare(strict_types=1);

namespace Drupal\niklan\Markup\Markdown\Extension;

use Drupal\niklan\Markup\Markdown\Renderer\FencedCodeRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\Config\ConfigurationBuilderInterface;

/**
 * @ingroup markdown
 */
final class NiklanMarkdownExtension implements ConfigurableExtensionInterface {

  #[\Override]
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new HeadingPermalinkExtension());
    $environment->addExtension(new ContainerBlockDirectiveExtension());
    $environment->addExtension(new LeafBlockDirectiveExtension());

    $environment->addRenderer(FencedCode::class, new FencedCodeRenderer(), 50);
  }

  #[\Override]
  public function configureSchema(ConfigurationBuilderInterface $builder): void {
    $builder->set('heading_permalink/insert', 'after');
  }

}
