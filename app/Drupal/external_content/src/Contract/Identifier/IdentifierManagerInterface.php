<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Identifier;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\IdentifiedSourceCollection;
use Drupal\external_content\Data\SourceCollection;

/**
 * {@selfdoc}
 */
interface IdentifierManagerInterface extends EnvironmentAwareInterface {

  /**
   * {@inheritdoc}
   */
  public function identify(SourceCollection $source_collection): IdentifiedSourceCollection;

}
