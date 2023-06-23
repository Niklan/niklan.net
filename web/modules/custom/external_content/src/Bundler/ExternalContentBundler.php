<?php declare(strict_types = 1);

namespace Drupal\external_content\Bundler;

use Drupal\external_content\Contract\Bundler\ExternalContentBundlerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\ExternalContentBundleCollection;
use Drupal\external_content\Data\ExternalContentDocumentCollection;

/**
 * Provides an external content bundler.
 */
final class ExternalContentBundler implements ExternalContentBundlerInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@inheritdoc}
   */
  public function bundle(ExternalContentDocumentCollection $document_collection): ExternalContentBundleCollection {
    // @todo Implement bundle() method.
    return new ExternalContentBundleCollection();
  }

}
