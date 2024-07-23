<?php

declare(strict_types=1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Converter\ConverterManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Exception\MissingContainerDefinitionException;
use Drupal\external_content\Exception\MissingConverterException;
use Drupal\external_content\Source\Html;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ConverterManager implements ConverterManagerInterface {

  public function __construct(
    private ContainerInterface $container,
    private array $converters = [],
  ) {}

  #[\Override]
  public function convert(SourceInterface $input, EnvironmentInterface $environment): Html {
    foreach ($environment->getConverters() as $converter) {
      \assert($converter instanceof ConverterInterface);
      $result = $converter->convert($input);

      if ($result->hasResult()) {
        return $result->getResult();
      }
    }

    throw new MissingConverterException($input, $environment);
  }

  #[\Override]
  public function get(string $converter_id): ConverterInterface {
    if (!$this->has($converter_id)) {
      throw new MissingContainerDefinitionException(
        type: 'converter',
        id: $converter_id,
      );
    }

    $service = $this->converters[$converter_id]['service'];

    return $this->container->get($service);
  }

  #[\Override]
  public function has(string $converter_id): bool {
    return \array_key_exists($converter_id, $this->converters);
  }

  #[\Override]
  public function list(): array {
    return $this->converters;
  }

}
