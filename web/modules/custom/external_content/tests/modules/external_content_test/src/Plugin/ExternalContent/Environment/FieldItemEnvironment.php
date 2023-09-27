<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Environment;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\BasicHtmlExtension;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentPlugin;

/**
 * Provides an environment for testing field item.
 *
 * @ExternalContentEnvironment(
 *   id = "field_item",
 *   label = @Translation("Field item environment"),
 * )
 */
final class FieldItemEnvironment extends EnvironmentPlugin {

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    $environment = new Environment();
    $environment->addExtension(new BasicHtmlExtension());

    return $environment;
  }

}
