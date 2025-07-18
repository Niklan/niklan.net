<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Exception;

final class PrimaryTranslationNotFoundException extends \LogicException {

  public function __construct() {
    parent::__construct('Primary translation not found');
  }

}
