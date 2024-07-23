<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\ExternalContent;

use Drupal\external_content\Contract\Builder\RenderArrayBuilderManagerInterface;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Converter\ConverterManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Extension\ExtensionManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderManagerInterface;
use Drupal\external_content\Contract\Parser\HtmlParserManagerInterface;
use Drupal\external_content\Contract\Serializer\SerializerManagerInterface;

interface ExternalContentManagerInterface {

  public function getEnvironmentManager(): EnvironmentManagerInterface;

  public function getExtensionManager(): ExtensionManagerInterface;

  public function getFinderManager(): FinderManagerInterface;

  public function getIdentifiersManager(): IdentifierManagerInterface;

  public function getBundlerManager(): BundlerManagerInterface;

  public function getConverterManager(): ConverterManagerInterface;

  public function getLoaderManager(): LoaderManagerInterface;

  public function getHtmlParserManager(): HtmlParserManagerInterface;

  public function getSerializerManager(): SerializerManagerInterface;

  public function getRenderArrayBuilderManager(): RenderArrayBuilderManagerInterface;

}
