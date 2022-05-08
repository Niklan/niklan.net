/**
 * @file
 * Mobile sidebar behaviors.
 */
(function (Drupal) {

  Drupal.behaviors.niklanMobileSidebar = {
    attach: function (context, settings) {
      let menuToggle = context.querySelector('.js-mobile-menu-toggle');
      let mobileSidebar = context.querySelector('.js-mobile-sidebar');
      let body = context.querySelector('.js-body');

      if (!menuToggle || !mobileSidebar || !body) {
        return;
      }

      if (!menuToggle.processed) {
        menuToggle.processed = true;

        menuToggle.addEventListener('click', function () {
          menuToggle.classList.toggle('is-active');
          mobileSidebar.classList.toggle('is-active');
          body.classList.toggle('is-mobile-sidebar-active');
        });
      }
    }
  };

})(Drupal);
