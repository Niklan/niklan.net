<?php declare(strict_types=1);

namespace Drupal\niklan\Loader;

use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Data\ContentBundle;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class BlogLoader implements LoaderInterface {

  /**
   * {@inheritdoc}
   */
  public function load(ContentBundle $bundle): LoaderResultInterface {
    // @todo
    // - Find existing content (new shouldn't be created)
    // - Go over content and upload/update media. Replace these content nodes
    //   by a new ones with Drupal specific info.
    // - Update external content field.
  }

}
