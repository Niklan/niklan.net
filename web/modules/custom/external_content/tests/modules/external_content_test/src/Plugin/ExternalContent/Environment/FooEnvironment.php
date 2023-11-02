<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Environment;

use Drupal\external_content\Builder\Html\ElementRenderArrayBuilder;
use Drupal\external_content\Builder\Html\PlainTextRenderArrayBuilder;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentPlugin;
use Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\Environment\EnvironmentPluginTest;

/**
 * Provides an environment for testing.
 *
 * @ExternalContentEnvironment(
 *   id = "foo",
 *   label = @Translation("Foo environment"),
 * )
 */
final class FooEnvironment extends EnvironmentPlugin {

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    $configuration = new Configuration([
      EnvironmentPluginTest::class => 'Oh, hello there!',
    ]);
    $environment = new Environment($configuration);
    $environment->addBuilder(new ElementRenderArrayBuilder());
    $environment->addBuilder(new PlainTextRenderArrayBuilder());

    return $environment;
  }

}
