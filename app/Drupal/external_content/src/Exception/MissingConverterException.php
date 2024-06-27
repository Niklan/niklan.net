<?php

declare(strict_types=1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * {@selfdoc}
 */
final class MissingConverterException extends \LogicException {

  /**
   * Constructs a new MissingSourceParserException instance.
   */
  public function __construct(
    public readonly SourceInterface $source,
    public readonly EnvironmentInterface $environment,
  ) {
    $available_parsers = \array_map(
      static fn (ConverterInterface $converter): string => $converter::class,
      \iterator_to_array($this->environment->getConverters()),
    );

    $message = \sprintf(
      "Conversion of type %s is not possible. No suitable converters are found. Available converters: %s",
      $this->source->type(),
      \implode(', ', $available_parsers),
    );
    parent::__construct($message);
  }

}
