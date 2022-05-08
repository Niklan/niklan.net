<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\niklan\Form\SidebarSearchForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a sidebar search form block.
 *
 * @Block(
 *   id = "niklan_node_sidebar_search_form",
 *   admin_label = @Translation("Sidebar search form"),
 *   category = @Translation("Custom")
 * )
 */
final class SidebarSearchFormBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The form builder.
   */
  protected FormBuilderInterface $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->formBuilder = $container->get('form_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [$this->formBuilder->getForm(SidebarSearchForm::class)];
  }

}
