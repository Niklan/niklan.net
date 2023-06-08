<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Loader;

use Drupal\external_content\Data\ExternalContent;
use Drupal\external_content\Plugin\ExternalContent\LoaderPlugin;

/**
 * Provides a loader for 'blog_entry' entity.
 *
 * @ExternalContentLoader(
 *   id = "content",
 *   label = @Translation("Blog Entry content"),
 * )
 *
 * @ingroup content_sync
 */
final class ContentLoader extends LoaderPlugin {

  /**
   * {@inheritdoc}
   */
  public function load(ExternalContent $external_content): void {
    // @todo Implement loading.
  }

}
