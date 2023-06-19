<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\EnvironmentInterface;
use Drupal\external_content\Contract\ExternalContentMarkupConverterInterface;
use Drupal\external_content\Contract\MarkupConverterInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Provides an external markup convert.
 */
final class ExternalContentMarkupConverter implements ExternalContentMarkupConverterInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function convert(ExternalContentFile $file): ExternalContentHtml {
    $result = new ExternalContentHtml(
      $file,
      $file->getContents(),
    );

    foreach ($this->environment->getMarkupConverters() as $converter) {
      \assert($converter instanceof MarkupConverterInterface);
      $result = $converter->convert($result);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): self {
    $this->environment = $environment;

    return $this;
  }

}
