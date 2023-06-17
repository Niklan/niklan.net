<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Extension;

use Drupal\niklan\CommonMark\Renderer\FencedCodeRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Provides a custom Markdown extension.
 *
 * @ingroup markdown
 */
final class NiklanMarkdownExtension implements ExtensionInterface {

  /**
   * {@inheritdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new EmbedExtension());

    $environment->addRenderer(FencedCode::class, new FencedCodeRenderer(), 1);
  }

}