<?php declare(strict_types = 1);

namespace Drupal\external_content\Exception;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * Provides an exception when there is no parser for a source.
 */
final class MissingSourceParserException extends \LogicException {

  /**
   * Constructs a new MissingSourceParserException instance.
   */
  public function __construct(
    public readonly SourceInterface $source,
    public readonly EnvironmentInterface $environment,
  ) {
    $available_parsers = \array_map(
      static fn (ParserInterface $parser): string => $parser::class,
      \iterator_to_array($this->environment->getParsers()),
    );

    $message = \sprintf(
      "Environment used for parsing source %s doesn't have any suitable parser. Available parsers: %s",
      \get_class($this->source),
      \implode(', ', $available_parsers),
    );
    parent::__construct($message);
  }

}
