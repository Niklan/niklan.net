<?php declare(strict_types = 1);

namespace Drupal\external_content\ExternalContent;

use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Converter\ConverterManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Extension\ExtensionManagerInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderManagerInterface;
use Drupal\external_content\Contract\Parser\HtmlParserManagerInterface;

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
    private ConverterManagerInterface $converterManager,
    private LoaderManagerInterface $loaderManager,
    private HtmlParserManagerInterface $htmlParserManager,
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
   * {@inheritdoc}
   */
  #[\Override]
  public function getIdentifiersManager(): IdentifierManagerInterface {
    return $this->identifierManager;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getBundlerManager(): BundlerManagerInterface {
    return $this->bundlerManager;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getExtensionManager(): ExtensionManagerInterface {
    return $this->extensionManager;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getConverterManager(): ConverterManagerInterface {
    return $this->converterManager;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function getLoaderManager(): LoaderManagerInterface {
    return $this->loaderManager;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getHtmlParserManager(): HtmlParserManagerInterface {
    return $this->htmlParserManager;
  }

}
