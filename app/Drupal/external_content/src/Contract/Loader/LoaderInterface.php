<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\LoaderResult;

/**
 * {@selfdoc}
 */
interface LoaderInterface {

  /**
   * {@selfdoc}
   */
  public function load(IdentifiedSourceBundle $bundle): LoaderResult;

}
