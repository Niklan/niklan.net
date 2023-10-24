<?php declare(strict_types=1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\Converter\ContentConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\File;

/**
 * {@selfdoc}
 */
final class ContentConverter implements ContentConverterInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function convert(File $file): Content {
    // TODO: Implement convert() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
