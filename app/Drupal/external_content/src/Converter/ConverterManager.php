<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Converter\ConverterManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Exception\MissingConverterException;
use Drupal\external_content\Source\Html;

/**
 * {@selfdoc}
 */
final class ConverterManager implements ConverterManagerInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function convert(SourceInterface $input): Html {
    foreach ($this->environment->getConverters() as $converter) {
      \assert($converter instanceof ConverterInterface);

      if ($converter->supportsConversion($input)) {
        return $converter->convert($input);
      }
    }

    throw new MissingConverterException($input, $this->environment);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function supportsConversion(SourceInterface $source): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
