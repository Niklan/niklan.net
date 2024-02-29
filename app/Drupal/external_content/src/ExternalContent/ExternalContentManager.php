<?php declare(strict_types = 1);

namespace Drupal\external_content\ExternalContent;

use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Extension\ExtensionManagerInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;

/**
 * {@selfdoc}
 */
final readonly class ExternalContentManager implements ExternalContentManagerInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private EnvironmentManagerInterface $environmentManager,
    private ExtensionManagerInterface $extensionManager,
    private FinderManagerInterface $finderManager,
    private IdentifierManagerInterface $identifierManager,
    private BundlerManagerInterface $bundlerManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getEnvironmentManager(): EnvironmentManagerInterface {
    return $this->environmentManager;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getFinderManager(): FinderManagerInterface {
    return $this->finderManager;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function getIdentifiersManager(): IdentifierManagerInterface {
    return $this->identifierManager;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function getBundlerManager(): BundlerManagerInterface {
    return $this->bundlerManager;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function getExtensionManager(): ExtensionManagerInterface {
    return $this->extensionManager;
  }

}
