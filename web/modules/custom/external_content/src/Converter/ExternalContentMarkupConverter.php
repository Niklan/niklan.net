<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\EnvironmentInterface;
use Drupal\external_content\Contract\ExternalContentMarkupConverterInterface;
use Drupal\external_content\Contract\MarkupConverterInterface;
use Drupal\external_content\Contract\MarkupConverterPostprocessorInterface;
use Drupal\external_content\Contract\MarkupConverterPreprocessorInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;

final class ExternalContentMarkupConverter implements ExternalContentMarkupConverterInterface {

  public function __construct(
    protected EnvironmentInterface $environment,
  ) {}

  public function convert(ExternalContentFile $file): ExternalContentHtml {
    $result = new ExternalContentHtml(
      $file,
      $file->getContents(),
    );

    foreach ($this->environment->getMarkupConverterPreprocessors() as $preprocessor) {
      \assert($preprocessor instanceof MarkupConverterPreprocessorInterface);
      $result = $preprocessor->preprocess($result);
    }

    foreach ($this->environment->getMarkupConverters() as $converter) {
      \assert($converter instanceof MarkupConverterInterface);
      $result = $converter->convert($result);
    }

    foreach ($this->environment->getMarkupConverterPostprocessors() as $postprocessor) {
      \assert($postprocessor instanceof MarkupConverterPostprocessorInterface);
      $result = $postprocessor->postprocess($result);
    }

    return $result;
  }

}
