<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Markup;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\MarkupPluginInterface;
use Drupal\niklan\CommonMark\Adapter\EmbedAdapter;
use Drupal\niklan\CommonMark\Extension\NiklanMarkdownExtension;
use League\CommonMark\Environment\Environment;
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
    $config = [
      'embed' => [
        'adapter' => new EmbedAdapter(),
        'allowed_domains' => ['youtube.com', 'youtu.be'],
      ],
    ];

    $environment = new Environment($config);
    $environment->addExtension(new NiklanMarkdownExtension());

    $converter = new MarkdownConverter($environment);

    return $converter->convert($content)->getContent();
  }

}
