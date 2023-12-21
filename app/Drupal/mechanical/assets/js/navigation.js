(function () {

  let navigationElement = document.querySelector('.js-navigation');
  let mobileButtonElement = document.querySelector('.js-navigation-mobile-toggle');
  let navigationCloseButtonElement = document.querySelector('.js-navigation-close');

  if (!navigationElement || !mobileButtonElement) {
    return;
  }

  if (navigationCloseButtonElement) {
    navigationCloseButtonElement.addEventListener('click', () => {
      navigationElement.classList.remove('is-active');
      document.querySelector('body').classList.remove('is-navigation-active');
    });
  }

  mobileButtonElement.addEventListener('click', function () {
    navigationElement.classList.toggle('is-active');
    document.querySelector('body').classList.toggle('is-navigation-active');
  });

})();
