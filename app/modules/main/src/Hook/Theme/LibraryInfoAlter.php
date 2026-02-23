<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Theme;

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Hook\Attribute\Hook;

#[Hook('library_info_alter')]
final class LibraryInfoAlter {

  public function __construct(
    protected ModuleExtensionList $moduleExtensionList,
  ) {}

  private function alterHighlightJs(array &$libraries, string $extension): void {
    if ($extension !== 'app_blog' || !isset($libraries['hljs'])) {
      return;
    }

    $module_path = $this->moduleExtensionList->getPath('app_main');
    $worker_path = "/$module_path/assets/js/hljs.worker.js";
    $libraries['hljs']['drupalSettings']['highlightJs']['workerPath'] = $worker_path;
  }

  public function __invoke(array &$libraries, string $extension): void {
    $this->alterHighlightJs($libraries, $extension);
  }

}
