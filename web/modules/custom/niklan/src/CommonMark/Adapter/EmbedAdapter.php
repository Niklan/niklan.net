<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Adapter;

use League\CommonMark\Extension\Embed\Embed;
use League\CommonMark\Extension\Embed\EmbedAdapterInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Provides an adapter for 'embed'.
 *
 * @ingroup markdown
 */
final class EmbedAdapter implements EmbedAdapterInterface {

  /**
   * {@inheritdoc}
   */
  public function updateEmbeds(array $embeds): void {
    foreach ($embeds as $embed) {
      \assert($embed instanceof Embed);
      $html = new HtmlElement('niklan-embed', [
        'href' => $embed->getUrl(),
      ]);

      $embed->setEmbedCode((string) $html);
    }
  }

}
