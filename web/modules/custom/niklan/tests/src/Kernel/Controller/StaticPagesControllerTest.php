<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Controller;

use Drupal\niklan\Controller\StaticPagesController;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;

/**
 * Provides a test for static pages.
 *
 * @coversDefaultClass \Drupal\niklan\Controller\StaticPagesController
 */
final class StaticPagesControllerTest extends NiklanTestBase {

  /**
   * The controller.
   */
  protected ?StaticPagesController $controller = NULL;

  /**
   * Provides a test for 'niklan.about' route.
   *
   * @covers ::about
   */
  public function testAbout(): void {
    $result = $this->controller->about();
    $this->assertEquals(['#theme' => 'niklan_about_page'], $result);
  }

  /**
   * Provides a test for 'niklan.support' route.
   *
   * @covers ::support
   */
  public function testSupport(): void {
    $result = $this->controller->support();
    $this->assertEquals(['#theme' => 'niklan_support_page'], $result);
  }

  /**
   * Provides a test for 'niklan.support' route.
   *
   * @covers ::services
   */
  public function testServices(): void {
    $result = $this->controller->services();
    $this->assertEquals(['#theme' => 'niklan_services_page'], $result);
  }

  /**
   * Provides a test for 'contact.site_page route.
   *
   * @covers ::contact
   */
  public function testContact(): void {
    $result = $this->controller->contact();
    $this->assertEquals(['#theme' => 'niklan_contact_page'], $result);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->controller = new StaticPagesController();
  }

}
