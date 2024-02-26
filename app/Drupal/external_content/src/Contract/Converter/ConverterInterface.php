<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Source\Html;

/**
 * {@selfdoc}
 */
interface ConverterInterface {

  /**
   * {@selfdoc}
   */
  public function convert(SourceInterface $input): Html;

  /**
   * {@selfdoc}
   */
  public function supportsConversion(SourceInterface $source): bool;

}
