<?php declare(strict_types=1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\File;

/**
 * {@selfdoc}
 */
interface ContentConverterInterface extends EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  public function convert(File $file): Content;

}
