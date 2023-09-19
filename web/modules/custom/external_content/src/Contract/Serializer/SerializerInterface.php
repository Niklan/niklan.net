<?php declare(strict_types=1);

namespace Drupal\external_content\Contract\Serializer;

use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Defines the interface for an External Content DOM serializer.
 */
interface SerializerInterface {

  /**
   * {@selfdoc}
   */
  public function serialize(ExternalContentDocument $document): string;

  /**
   * {@selfdoc}
   */
  public function deserialize(string $json): ExternalContentDocument;

}
