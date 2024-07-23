<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Kernel\Form;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\niklan\Controller\SearchControllerInterface;
use Drupal\niklan\Form\SearchForm;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a test for a custom site search form.
 *
 * @coversDefaultClass \Drupal\niklan\Form\SearchForm
 */
final class SearchFormTest extends NiklanTestBase {

  /**
   * The form builder.
   */
  protected FormBuilderInterface $formBuilder;

  /**
   * The request stack.
   */
  protected RequestStack $requestStack;

  /**
   * Tests basic form submission without values.
   */
  public function testSubmitFormEmpty(): void {
    $form_state = new FormState();

    $this->formBuilder->submitForm(SearchForm::class, $form_state);

    // Form state doesn't return redirect value if it's marked as programmed.
    $form_state->setProgrammed(FALSE);
    $redirect = $form_state->getRedirect();

    self::assertEquals('niklan.search_page', $redirect->getRouteName());
    self::assertEmpty($redirect->getOption('query'));
  }

  /**
   * Tests basic form submission with values.
   */
  public function testSubmitForm(): void {
    $form_state = new FormState();
    $form_state->setValue('query', 'Hello, World!');

    $this->formBuilder->submitForm(SearchForm::class, $form_state);

    // Form state doesn't return redirect value if it's marked as programmed.
    $form_state->setProgrammed(FALSE);
    $redirect = $form_state->getRedirect();

    self::assertEquals('niklan.search_page', $redirect->getRouteName());
    self::assertEquals(
      ['q' => 'Hello, World!'],
      $redirect->getOption('query'),
    );
  }

  /**
   * Tests that AJAX callback working as expected.
   */
  public function testAjaxCallback(): void {
    $expected_title = 'Hello, Title!';
    $expected_results = ['#markup' => 'search_results'];

    $search_controller = $this->prophesize(SearchControllerInterface::class);
    $search_controller->pageTitle(Argument::any())->willReturn($expected_title);
    $search_controller
      ->buildPageContent(Argument::any())
      ->willReturn($expected_results);

    $class_resolver = $this->prophesize(ClassResolverInterface::class);
    $class_resolver
      ->getInstanceFromDefinition(Argument::any())
      ->willReturn($search_controller->reveal());

    $request = $this->requestStack->getCurrentRequest();
    $request->query->set('q', 'foo');
    $request->query->set('page', '100');
    $request->query->set(FormBuilderInterface::AJAX_FORM_REQUEST, 1);
    $request->query->set(MainContentViewSubscriber::WRAPPER_FORMAT, 'ajax');
    $this->requestStack->push($request);

    $form_state = new FormState();
    $form_state->setValue('query', 'Hello, World!');

    $form_object = new SearchForm($class_resolver->reveal());
    $response = $form_object->onAjax([], $form_state);

    $request = $this->requestStack->getCurrentRequest();
    self::assertEquals('Hello, World!', $request->query->get('q'));
    self::assertNull($request->query->get('page'));
    self::assertNull($request->query->get(FormBuilderInterface::AJAX_FORM_REQUEST));
    self::assertNull($request->query->get(MainContentViewSubscriber::WRAPPER_FORMAT));

    $commands = $response->getCommands();
    self::assertCount(3, $commands);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->formBuilder = $this->container->get('form_builder');
    $this->requestStack = $this->container->get('request_stack');
  }

}
