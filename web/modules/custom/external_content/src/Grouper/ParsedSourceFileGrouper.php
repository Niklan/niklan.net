<?php

declare(strict_types = 1);

namespace Drupal\external_content\Grouper;

use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Dto\ParsedSourceFileCollection;
use Drupal\external_content\Plugin\ExternalContent\Grouper\GrouperPluginManagerInterface;

/**
 * Provides a default implementation for parsed source file grouper.
 */
final class ParsedSourceFileGrouper implements ParsedSourceFileGrouperInterface {

  /**
   * Constructs a new ParsedSourceFileGrouper object.
   *
   * @param \Drupal\external_content\Plugin\ExternalContent\Grouper\GrouperPluginManagerInterface $grouperPluginManager
   *   The grouper plugin manager.
   */
  public function __construct(
    protected GrouperPluginManagerInterface $grouperPluginManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function group(ParsedSourceFileCollection $parsed_source_files, string $grouper_id = 'params'): ExternalContentCollection {
    /** @var \Drupal\external_content\Plugin\ExternalContent\Grouper\GrouperInterface $plugin */
    $plugin = $this->grouperPluginManager->createInstance($grouper_id);

    return $plugin->group($parsed_source_files);
  }

}
