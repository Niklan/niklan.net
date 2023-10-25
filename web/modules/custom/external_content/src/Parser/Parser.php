<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Parser\ParserInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Exception\MissingSourceParserException;
use Drupal\external_content\Node\Content;

/**
 * {@selfdoc}
 */
final class Parser implements ParserInterface, EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@inheritdoc}
   */
  public function parse(SourceInterface $source): Content {
    foreach ($this->environment->getParsers() as $parser) {
      \assert($parser instanceof ParserInterface);

      if ($parser->supportsParse($source)) {
        return $parser->parse($source);
      }
    }

    throw new MissingSourceParserException($source, $this->environment);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsParse(SourceInterface $source): bool {
    return TRUE;
  }

}
