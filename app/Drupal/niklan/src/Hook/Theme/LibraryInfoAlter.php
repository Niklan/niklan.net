<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class LibraryInfoAlter implements ContainerInjectionInterface {

  public function __construct(
    protected ModuleExtensionList $moduleExtensionList,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('extension.list.module'),
    );
  }

  protected function alterDrupalAjax(array &$libraries, string $extension): void {
    if ($extension !== 'core' || !isset($libraries['drupal.ajax'])) {
      return;
    }

    $module_path = $this->moduleExtensionList->getPath('niklan');

    $js_path = "/$module_path/assets/js/command.ajax.js";
    $libraries['drupal.ajax']['js'][$js_path] = [];
  }

  public function __invoke(array &$libraries, string $extension): void {
    $this->alterDrupalAjax($libraries, $extension);
  }

}
