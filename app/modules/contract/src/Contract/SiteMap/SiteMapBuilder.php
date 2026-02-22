<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\SiteMap;

interface SiteMapBuilder {

  public function build(): SiteMap;

}
