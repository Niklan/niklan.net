<?php declare(strict_types = 1);

namespace Drupal\external_content\Loader;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Loader\LoaderFacadeInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\LoaderResult;

/**
 * {@selfdoc}
 */
final class LoaderFacade implements LoaderFacadeInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function load(ContentBundle $bundle): LoaderResultInterface {
    foreach ($this->environment->getLoaders() as $loader) {
      \assert($loader instanceof LoaderInterface);
      $result = $loader->load($bundle);

      if ($result->isSuccess() || !$result->shouldContinue()) {
        return $result;
      }
    }

    return LoaderResult::ignore();
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
