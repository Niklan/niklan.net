<?php

declare(strict_types=1);

namespace Drupal\niklan\Node\Entity;

final class Portfolio extends Node implements PortfolioInterface {

  public function getYearOfCompletion(): ?string {
    return $this->get('field_date')->first()?->get('date')->getValue()->format('Y');
  }

  /**
   * @return list<\Drupal\taxonomy\TermInterface>
   */
  public function getCategories(): array {
    return $this->get('field_portfolio_categories')->referencedEntities();
  }

}
