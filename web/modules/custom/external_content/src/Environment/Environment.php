<?php declare(strict_types = 1);

namespace Drupal\external_content\Environment;

use Drupal\external_content\Contract\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\EnvironmentInterface;
use Drupal\external_content\Contract\FinderInterface;
use Drupal\external_content\Contract\GrouperInterface;
use Drupal\external_content\Contract\HtmlParserInterface;
use Drupal\external_content\Contract\MarkupConverterInterface;
use Drupal\external_content\Contract\MarkupConverterPostprocessorInterface;
use Drupal\external_content\Contract\MarkupConverterPreprocessorInterface;
use Drupal\external_content\Data\Configuration;

final class Environment implements EnvironmentInterface, EnvironmentBuilderInterface {

  protected array $htmlParsers = [];

  protected array $groupers = [];

  protected array $markupConverters = [];

  protected array $markupConverterPreprocessors = [];

  protected array $markupConverterPostprocessors = [];

  protected array $finders = [];

  public function __construct(
    protected Configuration $configuration,
  ) {}

  public function addHtmlParser(HtmlParserInterface $parser): EnvironmentBuilderInterface {
    $this->htmlParsers[] = $parser;

    return $this;
  }

  public function addGrouper(GrouperInterface $grouper): EnvironmentBuilderInterface {
    $this->groupers[] = $grouper;

    return $this;
  }

  public function addMarkupConverter(MarkupConverterInterface $converter): EnvironmentBuilderInterface {
    $this->markupConverters[] = $converter;

    return $this;
  }

  public function getHtmlParsers(): iterable {
    return $this->htmlParsers;
  }

  public function getGroupers(): iterable {
    return $this->groupers;
  }

  public function getMarkupConverters(): iterable {
    return $this->markupConverters;
  }

  public function getConfiguration(): Configuration {
    return $this->configuration;
  }

  public function addFinder(FinderInterface $finder): EnvironmentBuilderInterface {
    $this->finders[] = $finder;

    return $this;
  }

  public function getFinders(): iterable {
    return $this->finders;
  }

  public function addMarkupConverterPreprocessor(MarkupConverterPreprocessorInterface $preprocessor): EnvironmentBuilderInterface {
    $this->markupConverterPreprocessors[] = $preprocessor;

    return $this;
  }

  public function getMarkupConverterPreprocessors(): iterable {
    return $this->markupConverterPreprocessors;
  }

  public function addMarkupConverterPostprocessor(MarkupConverterPostprocessorInterface $postprocessor): EnvironmentBuilderInterface {
    $this->markupConverterPostprocessors[] = $postprocessor;

    return $this;
  }

  public function getMarkupConverterPostprocessors(): iterable {
    return $this->markupConverterPostprocessors;
  }

}
