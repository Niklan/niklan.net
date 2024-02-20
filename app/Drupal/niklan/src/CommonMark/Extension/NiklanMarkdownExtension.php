<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Extension;

use Drupal\niklan\CommonMark\Block\Note;
use Drupal\niklan\CommonMark\Parser\NoteStartParser;
use Drupal\niklan\CommonMark\Renderer\FencedCodeRenderer;
use Drupal\niklan\CommonMark\Renderer\NoteRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
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
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new ContainerBlockDirectiveExtension());
    $environment->addExtension(new LeafBlockDirectiveExtension());

    // Should be higher than BlockQuoteStartParser which is having 70 priority.
    $environment->addBlockStartParser(new NoteStartParser(), 80);
    $environment->addRenderer(Note::class, new NoteRenderer(), 80);

    $environment->addRenderer(FencedCode::class, new FencedCodeRenderer(), 50);
  }

}
