<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\ExternalContent;

use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Converter\ConverterManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentManagerInterface;
use Drupal\external_content\Contract\Extension\ExtensionManagerInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Identifier\IdentifierManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderManagerInterface;
use Drupal\external_content\Contract\Parser\HtmlParserManagerInterface;

/**
 * {@selfdoc}
 */
interface ExternalContentManagerInterface {

  /**
   * {@selfdoc}
   */
  public function getEnvironmentManager(): EnvironmentManagerInterface;

  /**
   * {@selfdoc}
   */
  public function getExtensionManager(): ExtensionManagerInterface;

  /**
   * {@selfdoc}
   */
  public function getFinderManager(): FinderManagerInterface;

  /**
   * {@selfdoc}
   */
  public function getIdentifiersManager(): IdentifierManagerInterface;

  /**
   * {@selfdoc}
   */
  public function getBundlerManager(): BundlerManagerInterface;

  /**
   * {@selfdoc}
   */
  public function getConverterManager(): ConverterManagerInterface;

  /**
   * {@selfdoc}
   */
  public function getLoaderManager(): LoaderManagerInterface;

  /**
   * {@selfdoc}
   */
  public function getHtmlParserManager(): HtmlParserManagerInterface;

}
