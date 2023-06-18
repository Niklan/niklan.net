<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

/**
 * Provides an interface for environment builder.
 */
interface EnvironmentBuilderInterface {

  public function addHtmlParser(HtmlParserInterface $parser): self;

  public function addGrouper(GrouperInterface $grouper): self;

  public function addMarkupConverter(MarkupConverterInterface $converter): self;

  public function addMarkupConverterPreprocessor(MarkupConverterPreprocessorInterface $preprocessor): self;

  public function addMarkupConverterPostprocessor(MarkupConverterPostprocessorInterface $postprocessor): self;

  /**
   *
   */
  public function addFinder(FinderInterface $finder): self;

}
