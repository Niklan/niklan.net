<?php declare(strict_types = 1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Site\Settings;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides initial External ID value for existing content.
 *
 * @see niklan_deploy_0002()
 */
final class Deploy0002 implements ContainerInjectionInterface {

  /**
   * Constructs a new Deploy0002 instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Implements hook_deploy_HOOK().
   */
  public function __invoke(array &$sandbox): string {
    $this->prepareBatch($sandbox);

    if ($sandbox['total'] === 0) {
      $sandbox['#finished'] = 1;

      return 'No blog posts were found.';
    }

    $this->processBatch($sandbox);

    return (string) new FormattableMarkup('@current of @total blog posts are processed.', [
      '@current' => $sandbox['current'],
      '@total' => $sandbox['total'],
    ]);
  }

  /**
   * Prepares variables for batch if they are not initialized.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function prepareBatch(array &$sandbox): void {
    if (isset($sandbox['total'])) {
      return;
    }

    $sandbox['total'] = $this->getQuery()->count()->execute();
    $sandbox['current'] = 0;
    $sandbox['limit'] = Settings::get('entity_update_batch_size', 50);
  }

  /**
   * Process a single batch.
   *
   * @param array $sandbox
   *   The batch sandbox.
   */
  protected function processBatch(array &$sandbox): void {
    $ids = $this
      ->getQuery()
      ->range($sandbox['current'], $sandbox['limit'])
      ->execute();

    $blog_posts = $this
      ->entityTypeManager
      ->getStorage('node')
      ->loadMultiple($ids);

    foreach ($blog_posts as $blog_post) {
      \assert($blog_post instanceof BlogEntryInterface);

      $external_id = $this->getExternalIdMapping()[$blog_post->id()];
      $blog_post->setExternalId($external_id);
      $blog_post->save();

      $sandbox['current']++;
    }

    $sandbox['#finished'] = $sandbox['current'] / $sandbox['total'];
  }

  /**
   * Builds a default query for update.
   */
  protected function getQuery(): QueryInterface {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->sort('nid');
  }

  /**
   * {@selfdoc}
   */
  protected function getExternalIdMapping(): array {
    // 'nid' => 'external-id'.
    /* spellchecker: disable */
    return [
      '1' => 'hello-world',
      '2' => 'ubuntu-12-web-server',
      '3' => 'drupal-coding-standards-netbeans',
      '4' => 'drupal-7-windows-setup',
      '5' => 'tvs-are-better-than-internet-explorer-10',
      '6' => 'drupal-7-for-beginners-introduction-what-is-drupal',
      '7' => 'drupal-7-for-beginners-toolbar-shortcuts-administration',
      '8' => 'drupal-7-for-beginners-translating-drupal',
      '9' => 'drupal-7-for-beginners-working-with-content',
      '11' => 'drupal-7-for-beginners-configuring-site',
      '12' => 'drupal-7-for-beginners-creating-custom-content-types',
      '13' => 'drupal-7-for-beginners-working-with-fields',
      '14' => 'drupal-7-for-beginners-display-management-and-image-handling',
      '15' => 'drupal-7-for-beginners-taxonomy',
      '16' => 'drupal-7-for-beginners-blocks-regions',
      '17' => 'drupal-7-for-beginners-modules',
      '18' => 'drupal-7-for-beginners-views',
      '19' => 'drupal-7-for-beginners-path-aliases',
      '20' => 'drupal-7-for-beginners-contact-form',
      '21' => 'drupal-7-for-beginners-updating-drupal-core',
      '22' => 'drush-installation-and-usage-on-ubuntu-and-drupalhosting',
      // @todo Continue from there.
      '23' => 'drupal-7-code-style-check-ubuntu',
      '24' => 'drupal-7-catalog-with-views-panels',
      '25' => 'drupal-7-fix-encrypted-connection-error',
      '26' => 'fix-adsense-not-loading',
      '27' => 'drupal-7-integrate-color-module-into-theme',
      '28' => 'drupal-7-create-customer-support-system',
      '29' => 'drupal-7-views-grid-with-div',
      '30' => 'drupal-7-print-menu-programmatically',
      '31' => 'ubuntu-13.04-fix-bash-scripts',
      '32' => 'drupal-7-create-feature',
      '33' => 'drupal-7-block-content-access-with-rules',
      '34' => 'simple-way-to-translate-drupal-7',
      '35' => 'drupal-7-create-like-button-with-rate-module',
      '36' => 'smooth-transition-on-page-change',
      '37' => 'drupal-7-advanced-contact-page',
      '38' => 'drupal-7-boost',
      '39' => 'drupal-7-attach-javascript',
      '40' => 'prepare-project-for-drupal.org',
      '41' => 'drupal-7-create-callback-form-with-webform',
      '42' => 'mappy-easy-map-embedding',
      '43' => 'mappy-7.x-1.2',
      '44' => 'a-look-at-the-drupal-8-alpha',
      '46' => 'drupalstorm',
      '47' => 'drupal-7-hierarchical-path-aliases',
      '48' => 'drupal-7-crop-image-without-cut',
      '49' => 'mappy-7.x-1.3',
      '50' => 'drupal-7-custom-theme-settings',
      '51' => 'how-to-fix-sa-core-2013-003',
      '52' => 'add-fonts-into-google-docs',
      '53' => 'drupal-7-follow-functionality-with-flag',
      '54' => 'drupal-7-install-and-configure-ckeditor',
      '55' => 'indexisto-drupal-module',
      '56' => '3-years-width-drupal',
      '57' => 'drupalife-store-preview',
      '58' => 'drupal-7-create-distribution',
      '59' => 'blog-is-1-year',
      '60' => 'drupal-7-programmatically-render-entityform',
      '61' => 'drupalife-store-edit-and-update',
      '62' => 'drupal-7-theme-checkboxes-and-radios',
      '63' => 'drupal-7-node-grants-and-access',
      '64' => 'drupal-8-api-changes',
      '65' => 'mappy-7.x-1.4-8.x-1.0-rc1',
      '66' => 'drupal-8-hello-world',
      '67' => 'drupal-8-render-menu-programmatically',
      '68' => 'dru.io-drupal-community',
      '69' => 'digitalocean-mini-hosting',
      '70' => 'pushbullet-api-width-drupal',
      '71' => 'd7-library-download-command',
      '72' => 'd8-libraries-api',
      '73' => 'd8-form-api',
      '74' => 'd8-state-api',
      '75' => 'd8-block-plugin',
      '76' => 'd8-filter-plugin',
      '77' => 'd8-autocomplete',
      '78' => 'd8-tour-api',
      '79' => 'd8-queue-api',
      '80' => 'd8-print-contact-form',
      '81' => 'd8-add-metatags-programmatically',
      '82' => 'd8-create-entities-programmatically',
      '83' => 'd8-json-ld',
      '84' => 'd7-create-display-suite-field',
      '85' => 'd8-create-display-suite-plugin',
      '86' => 'ds-fg-poc-theming',
      '124' => 'd8-print-eform',
      '125' => 'd8-modal-api',
      '126' => 'd8-theme-negotiator',
      '127' => 'd8-ajax-validate-submit',
      '128' => 'views-group-content',
      '129' => 'd8-breadcrumb-builder',
      '130' => 'd8-composer',
      '131' => 'd8-html-mail-contact',
      '132' => 'd8-custom-field-type',
      '133' => 'd8-custom-csv-import',
      '134' => 'd8-custom-plugins',
      '135' => 'raspberry-pi-3-smart-tv',
      '136' => 'd8-csv-import-optimization',
      '137' => 'd8-custom-configure-section',
      '138' => 'd8-custom-toolbar-item',
      '139' => 'd7-d8-imagemagick',
      '145' => 'd7-custom-commerce-license-type',
      '146' => 'kodi-quasar',
      '147' => 'drupal-vm-ubuntu',
      '149' => 'oh-my-zsh',
      '150' => 'd8-services',
      '151' => 'd8-hook-theme',
      '152' => 'd7-d8-custom-list-options',
      '153' => 'd8-language-negation',
      '154' => 'd8-lazy-builder',
      '155' => 'd8-cache-tags-context-max-age',
      '156' => 'd7-views-field-handler-plugin',
      '157' => 'd8-twig-extension',
      '158' => 'drupal-commerce-2-price-resolver',
      '159' => 'd8-migrate-api',
      '165' => 'd8-rest-plugin',
      '166' => 'd8-authentication-api',
      '170' => 'd8-events',
      '171' => 'd8-route-subscriber',
      '172' => 'docker4drupal-ubuntu',
      '173' => 'd8-taxonomy-term-page-override',
      '174' => 'd8-search-api-custom-data',
      '175' => 'd8-paragraphs-behavior-plugin',
      '176' => 'd8-search-api-programmatically',
      '177' => 'd8-extra-field',
      '178' => 'd8-prev-next-buttons',
      '179' => 'd8-configuration-schema',
      '181' => 'd8-condition-plugin',
      '183' => 'd8-inbound-outbound-processor',
      '184' => 'd8-hooks',
      '185' => 'd8-two-ways-of-install',
      '186' => 'develop-and-deploy-2018',
      '187' => 'd8-middleware-api',
      '188' => 'd8-temp-store',
      '189' => 'drupal-commerce-2-workflows',
      '190' => 'd8-blog-from-scratch',
      '191' => 'd8-tokens',
      '192' => 'd8-batch-api',
      '199' => 'd8-access-check',
      '200' => 'd8-queue-worker',
      '201' => 'd8-user-data-service',
      '202' => 'd8-empty-field-value',
      '203' => 'd8-derivatives',
      '204' => 'd8-tagged-services',
      '205' => 'd9-custom-generator',
      '206' => 'd8-entity-reference-selection',
      '207' => 'd8-custom-module-translations-deploy',
      '208' => 'd8-main-content-renderer',
      '209' => 'migrate-from-drupal-composer-drupal-project',
      '210' => 'd8-render-arrays',
      '211' => 'd8-d9-lock-services',
      '213' => 'd8-d9-placeholder-strategy',
      '214' => 'd8-d9-oop-mail',
      '215' => 'why-you-should-try-drupal-9',
      '216' => 'drupal-warmer-2',
    ];
    /* spellchecker: enable */
  }

}
