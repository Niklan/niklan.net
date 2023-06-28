<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a DTO for export process state storage.
 */
final class ExportState {

  /**
   * Constructs a new ExportState instance.
   *
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   The CLI output instance.
   * @param string $destination
   *   The destination directory.
   */
  public function __construct(
    protected OutputInterface $output,
    protected string $destination,
  ) {}

  /**
   * Gets output.
   */
  public function getOutput(): OutputInterface {
    return $this->output;
  }

  /**
   * Gets the export destination.
   */
  public function getDestination(): string {
    return $this->destination;
  }

}
