<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

use Drupal\external_content\Data\ContentBundle;

/**
 * {@selfdoc}
 */
interface LoaderInterface {

  /**
   * {@selfdoc}
   */
  public function load(ContentBundle $bundle): LoaderResultInterface;

}
