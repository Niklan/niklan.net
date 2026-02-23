/**
 * @file
 * Mobile sidebar behaviors.
 */
((Drupal) => {

  Drupal.behaviors.niklanMobileSidebar = {
    attach(context) {
      const menuToggle = context.querySelector('.js-mobile-menu-toggle');
      const mobileSidebar = context.querySelector('.js-mobile-sidebar');
      const body = context.querySelector('.js-body');

      if (!menuToggle || !mobileSidebar || !body) {
        return;
      }

      if (!menuToggle.processed) {
        menuToggle.processed = true;

        menuToggle.addEventListener('click', () => {
          menuToggle.classList.toggle('is-active');
          mobileSidebar.classList.toggle('is-active');
          body.classList.toggle('is-mobile-sidebar-active');
        });
      }
    }
  };

})(Drupal);
