<?php declare(strict_types = 1);

namespace Drupal\external_content\Converter;

use Drupal\external_content\Contract\Converter\ExternalContentMarkupConverterInterface;
use Drupal\external_content\Contract\Converter\MarkupConverterInterface;
use Drupal\external_content\Contract\Converter\MarkupPostConverterInterface;
use Drupal\external_content\Contract\Converter\MarkupPreConverterInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface;

/**
 * Provides an external markup convert.
 */
final class ExternalContentMarkupConverter implements ExternalContentMarkupConverterInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * Constructs a new ExternalContentMarkupConverter instance.
   *
   * @param \Drupal\external_content\DependencyInjection\EnvironmentAwareClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected EnvironmentAwareClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function convert(ExternalContentFile $file): ExternalContentHtml {
    $result = new ExternalContentHtml($file, $file->getContents());

    foreach ($this->environment->getMarkupPreConverters() as $pre_converter) {
      $instance = $this->classResolver->getInstance(
        $pre_converter,
        MarkupPreConverterInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof MarkupPreConverterInterface);
      $instance->preConvert($result);
    }

    foreach ($this->environment->getMarkupConverters() as $converter) {
      $instance = $this->classResolver->getInstance(
        $converter,
        MarkupConverterInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof MarkupConverterInterface);
      $instance->convert($result);
    }

    foreach ($this->environment->getMarkupPostConverters() as $post_converter) {
      $instance = $this->classResolver->getInstance(
        $post_converter,
        MarkupPostConverterInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof MarkupPostConverterInterface);
      $instance->postConvert($result);
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
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
