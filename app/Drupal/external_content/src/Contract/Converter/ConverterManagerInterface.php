<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Converter;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Source\Html;

/**
 * {@selfdoc}
 */
interface ConverterManagerInterface {

  /**
   * {@selfdoc}
   */
  public function convert(SourceInterface $input, EnvironmentInterface $environment): Html;

  /**
   * {@selfdoc}
   */
  public function get(string $converter_id): ConverterInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $converter_id): bool;

  /**
   * {@selfdoc}
   *
   * @return array{
   *   service: string,
   *    id: string,
   *    }
   */
  public function list(): array;

}
