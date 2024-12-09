<?php

declare(strict_types=1);

namespace Drupal\niklan\SiteMap\Structure;

interface SiteMapBuilderInterface {

  public function build(): SiteMap;

}
