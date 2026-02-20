<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Composer\InstalledVersions;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Hook\Attribute\Hook;

#[Hook('library_info_alter')]
final class LibraryInfoAlter {

  public function __construct(
    protected ModuleExtensionList $moduleExtensionList,
  ) {}

  private function alterDrupalAjax(array &$libraries, string $extension): void {
    if ($extension !== 'core' || !isset($libraries['drupal.ajax'])) {
      return;
    }

    $module_path = $this->moduleExtensionList->getPath('niklan');

    $js_path = "/$module_path/assets/js/command.ajax.js";
    $libraries['drupal.ajax']['js'][$js_path] = [];
  }

  private function alterHighlightJs(array &$libraries, string $extension): void {
    if ($extension !== 'niklan' || !isset($libraries['hljs'])) {
      return;
    }

    $module_path = $this->moduleExtensionList->getPath('niklan');
    $worker_path = "/$module_path/assets/js/hljs.worker.js";
    $libraries['hljs']['drupalSettings']['highlightJs']['workerPath'] = $worker_path;
  }

  /**
   * Excludes PhotoSwipe JS from aggregation for better caching.
   *
   * The contrib module bundles both files into the page aggregate. Since these
   * are vendor assets (~67 KB) that rarely change, loading them as separate
   * scripts gives each file its own long-lived browser cache entry.
   *
   * The library version is replaced with the actual package version from
   * niklan-asset/photoswipe. The contrib module sets it to VERSION (Drupal
   * core version), which would not bust the browser cache on library updates.
   */
  private function alterPhotoswipe(array &$libraries, string $extension): void {
    if ($extension !== 'photoswipe' || !isset($libraries['photoswipe.local'])) {
      return;
    }

    $version = InstalledVersions::getVersion('niklan-asset/photoswipe');
    $libraries['photoswipe.local']['version'] = $version;

    $js_files = [
      '/libraries/photoswipe/dist/umd/photoswipe.umd.min.js',
      '/libraries/photoswipe/dist/umd/photoswipe-lightbox.umd.min.js',
    ];

    foreach ($js_files as $js_file) {
      if (!isset($libraries['photoswipe.local']['js'][$js_file])) {
        continue;
      }

      $libraries['photoswipe.local']['js'][$js_file]['preprocess'] = FALSE;
    }
  }

  public function __invoke(array &$libraries, string $extension): void {
    $this->alterDrupalAjax($libraries, $extension);
    $this->alterHighlightJs($libraries, $extension);
    $this->alterPhotoswipe($libraries, $extension);
  }

}
