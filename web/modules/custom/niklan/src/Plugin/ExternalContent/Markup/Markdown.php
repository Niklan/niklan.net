<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Markup;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\MarkupPluginInterface;
use Drupal\niklan\CommonMark\NiklanMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Provides a plugin for Markdown syntax.
 *
 * @ExternalContentMarkup(
 *   id = "niklan_markdown",
 *   label = @Translation("Markdown"),
 *   markup_identifiers = {"md", "markdown"},
 * )
 *
 * @ingroup markdown
 */
final class Markdown extends PluginBase implements MarkupPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(string $content): string {
    $environment = new Environment();
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new NiklanMarkdownExtension());

    $converter = new MarkdownConverter($environment);

    return $converter->convert($content)->getContent();
  }

}
