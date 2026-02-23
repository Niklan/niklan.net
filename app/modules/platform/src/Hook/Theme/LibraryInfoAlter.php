<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook\Theme;

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

    $module_path = $this->moduleExtensionList->getPath('app_platform');

    $js_path = "/$module_path/assets/js/command.ajax.js";
    $libraries['drupal.ajax']['js'][$js_path] = [];
  }

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
    $this->alterPhotoswipe($libraries, $extension);
  }

}
