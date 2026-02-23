<?php

declare(strict_types=1);

namespace Drupal\app_blog\Markup\Markdown\Extension;

use Drupal\app_blog\Markup\Markdown\Renderer\FencedCodeRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\Config\ConfigurationBuilderInterface;

/**
 * @ingroup markdown
 */
final class ArticleMarkdownExtension implements ConfigurableExtensionInterface {

  #[\Override]
  public function register(EnvironmentBuilderInterface $environment): void {
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new HeadingPermalinkExtension());
    $environment->addExtension(new ContainerBlockDirectiveExtension());
    $environment->addExtension(new LeafBlockDirectiveExtension());
    $environment->addExtension(new FootnoteExtension());

    $environment->addRenderer(FencedCode::class, new FencedCodeRenderer(), 50);
  }

  #[\Override]
  public function configureSchema(ConfigurationBuilderInterface $builder): void {
    $builder->set('heading_permalink/insert', 'after');

    // Prevents unwanted line breaks in rendered HTML. Markdown line breaks are
    // for source formatting (e.g., 80-char width) but add bloat in HTML output.
    //
    // Default behavior:
    // @code
    // <h2>Title</h2>\n
    // <p>Text with\nline breaks</p>\n
    // @endcode
    //
    // With these settings:
    // @code
    // <h2>Title</h2><p>Text with line breaks</p>
    // @endcode
    //
    // Note: it won't remove line breaks from code blocks and hard-breaks <br>.
    //
    // For some reason it is no longer documented, so check the source code:
    // @see \League\CommonMark\Environment\Environment::createDefaultConfiguration
    $builder->set('renderer', [
      'block_separator' => '',
      'inner_separator' => '',
      // Note that soft break must be a space, or words will be merged.
      'soft_break' => ' ',
    ]);

    // Default prefixes are not valid CSS IDs (with ':' char).
    $builder->set('footnote', [
      'ref_id_prefix' => 'fn-ref-',
      'footnote_id_prefix' => 'fn-',
    ]);
  }

}
