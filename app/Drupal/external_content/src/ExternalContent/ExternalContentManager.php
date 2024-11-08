<?php

declare(strict_types=1);

namespace Drupal\external_content\ExternalContent;

use Drupal\external_content\Contract\Builder\RenderArrayBuilderManagerInterface;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Converter\ConverterManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Extension\ExtensionManagerInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderManagerInterface;
use Drupal\external_content\Contract\Parser\HtmlParserManagerInterface;
use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;

/**
 * @todo Refactor managers using AutowireLocator.
 */
final readonly class ExternalContentManager implements ExternalContentManagerInterface {

  public function __construct(
    private EnvironmentManagerInterface $environmentManager,
    private ExtensionManagerInterface $extensionManager,
    private FinderManagerInterface $finderManager,
    private IdentifierManagerInterface $identifierManager,
    private BundlerManagerInterface $bundlerManager,
    private ConverterManagerInterface $converterManager,
    private LoaderManagerInterface $loaderManager,
    private HtmlParserManagerInterface $htmlParserManager,
    private SerializerManagerInterface $serializerManager,
    private RenderArrayBuilderManagerInterface $renderArrayBuilderManager,
  ) {}

  #[\Override]
  public function getEnvironmentManager(): EnvironmentManagerInterface {
    return $this->environmentManager;
  }

  #[\Override]
  public function getFinderManager(): FinderManagerInterface {
    return $this->finderManager;
  }

  #[\Override]
  public function getIdentifiersManager(): IdentifierManagerInterface {
    return $this->identifierManager;
  }

  #[\Override]
  public function getBundlerManager(): BundlerManagerInterface {
    return $this->bundlerManager;
  }

  #[\Override]
  public function getExtensionManager(): ExtensionManagerInterface {
    return $this->extensionManager;
  }

  #[\Override]
  public function getConverterManager(): ConverterManagerInterface {
    return $this->converterManager;
  }

  #[\Override]
  public function getLoaderManager(): LoaderManagerInterface {
    return $this->loaderManager;
  }

  #[\Override]
  public function getHtmlParserManager(): HtmlParserManagerInterface {
    return $this->htmlParserManager;
  }

  #[\Override]
  public function getSerializerManager(): SerializerManagerInterface {
    return $this->serializerManager;
  }

  #[\Override]
  public function getRenderArrayBuilderManager(): RenderArrayBuilderManagerInterface {
    return $this->renderArrayBuilderManager;
  }

}
