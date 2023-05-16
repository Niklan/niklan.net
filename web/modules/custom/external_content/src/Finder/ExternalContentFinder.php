<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\ExternalContentFinderInterface;
use Drupal\external_content\Contract\ParsedSourceFileGrouperInterface;
use Drupal\external_content\Contract\SourceFileFinderInterface;
use Drupal\external_content\Data\ExternalContentCollection;
use Drupal\external_content\Data\ParsedSourceFileCollection;
use Drupal\external_content\Data\SourceConfiguration;
use Drupal\external_content\Parser\SourceFileParser;

/**
 * Provides default implementation for external content finder.
 */
final class ExternalContentFinder implements ExternalContentFinderInterface {

  /**
   * Constructs a new ExternalContentFinderTest object.
   *
   * @param \Drupal\external_content\Contract\SourceFileFinderInterface $sourceFileFinder
   *   The source file finder.
   * @param \Drupal\external_content\Parser\SourceFileParser $sourceFileParser
   *   The source file parser.
   * @param \Drupal\external_content\Contract\ParsedSourceFileGrouperInterface $parsedSourceFileGrouper
   *   The parsed source file grouper.
   */
  public function __construct(
    protected SourceFileFinderInterface $sourceFileFinder,
    protected SourceFileParser $sourceFileParser,
    protected ParsedSourceFileGrouperInterface $parsedSourceFileGrouper,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function find(SourceConfiguration $source): ExternalContentCollection {
    $source_files = $this->sourceFileFinder->find($source->getWorkingDir());
    $parsed_source_files = new ParsedSourceFileCollection();

    foreach ($source_files as $source_file) {
      $parsed_source_file = $this->sourceFileParser->parse($source_file);
      $parsed_source_files->add($parsed_source_file);
    }

    return $this->parsedSourceFileGrouper->group(
      $parsed_source_files,
      $source->getGroupingPluginId(),
    );
  }

}
