/**
 * Adds special class for body element when mouse used for navigation, and when keyboard to adjust focus elements.
 */

// It is a mouse unless it's not. :)
document.body.classList.add('is-using-mouse');

document.body.addEventListener('mousedown', () => {
  document.body.classList.add('is-using-mouse');
});

// Re-enable focus styling when Tab is pressed
document.body.addEventListener('keydown', (event) => {
  if (event.key === 'Tab') {
    document.body.classList.remove('is-using-mouse');
  }
});
