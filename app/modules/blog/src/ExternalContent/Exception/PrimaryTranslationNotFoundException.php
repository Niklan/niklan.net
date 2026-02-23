<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Exception;

final class PrimaryTranslationNotFoundException extends \LogicException {

  public function __construct() {
    parent::__construct('Primary translation not found');
  }

}
