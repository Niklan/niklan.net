<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark;

use Drupal\niklan\Renderer\FencedCodeRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
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
    $environment->addRenderer(FencedCode::class, new FencedCodeRenderer(), 1);
  }

}
