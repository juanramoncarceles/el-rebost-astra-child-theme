/******************************************************************************
 ***************************** STICKY HEADER **********************************
 *****************************************************************************/
/*
// Get a reference to the main menu container.
const mainHeaderBar = document.querySelector('.main-header-bar');

// Add or remove a css class on the main menu container.
let stickyActive = false;
window.addEventListener('scroll', () => {
  if (window.scrollY == 0) {
    mainHeaderBar.classList.remove('sticky-active');
    stickyActive = false;
  } else if (!stickyActive) {
    mainHeaderBar.classList.add('sticky-active');
    stickyActive = true;
  }
});
*/


/******************************************************************************
 ******************** CHANGE DEFAULT SEARCH BAR BEHAVIOUR *********************
 *****************************************************************************/

(function () {
  // Get the menu item that contains the existing search bar.
  const menuSearchItem = document.querySelector('.rebost-custom-search');
  if (menuSearchItem) {
    const existingContainer = menuSearchItem.querySelector('.ysm-search-widget');
    if (existingContainer) {
      // Create the icon that will toggle the existing bar.
      const searchIcon = document.createElement('button'); // TODO use button element instead
      searchIcon.classList.add('rebost-search-icon');
      searchIcon.innerHTML = '<svg height="24" viewBox="0 0 24 24" width="24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>';
      searchIcon.addEventListener('click', () => existingContainer.classList.toggle('open'));      
      menuSearchItem.prepend(searchIcon);
    } else {
      console.warn('Item doesnt exist');
    }
  } else {
    console.warn('Search bar couldn\'t be found on the main menu.');
  } 
})();

