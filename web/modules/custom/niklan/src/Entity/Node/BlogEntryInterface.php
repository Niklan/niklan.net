<?php declare(strict_types = 1);

namespace Drupal\niklan\Entity\Node;

use Drupal\external_content\Data\ParsedSourceFile;

/**
 * Defines an interface for 'blog_entry' bundle class.
 */
interface BlogEntryInterface extends NodeInterface {

  /**
   * Sets the external ID.
   *
   * @param string $external_id
   *   The external ID.
   *
   * @return $this
   */
  public function setExternalId(string $external_id): self;

  /**
   * Gets the external ID.
   *
   * @return string
   *   The external ID.
   */
  public function getExternalId(): string;

  /**
   * Sets external content.
   *
   * @param \Drupal\external_content\Data\ParsedSourceFile $parsed_source_file
   *   The parsed source file.
   *
   * @return $this
   */
  public function setExternalContent(ParsedSourceFile $parsed_source_file): self;

}
