<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Transformer;

use Drupal\external_content\Node\RootNode;

interface Transformer {

  public function transform(TransformerContext $context): RootNode;

}
