/**
 * Navigation & Menu Interactions
 *
 * Handles mobile menu toggle, dropdown interactions, and accessibility
 */

( function() {
	'use strict';

	/**
	 * Mobile Menu Toggle Handler
	 */
	function initMobileMenuToggle() {
		const menuButtons = document.querySelectorAll(
			'.wp-block-navigation__responsive-container-open'
		);
		const closeButtons = document.querySelectorAll(
			'.wp-block-navigation__responsive-container-close'
		);
		const menuContainers = document.querySelectorAll(
			'.wp-block-navigation__responsive-container'
		);

		// Open menu
		menuButtons.forEach( ( button ) => {
			button.addEventListener( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				const container = this.closest(
					'.wp-block-navigation__responsive-container'
				);
				if ( container ) {
					container.classList.add( 'is-menu-open' );
				}
			} );
		} );

		// Close menu
		closeButtons.forEach( ( button ) => {
			button.addEventListener( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				const container = this.closest(
					'.wp-block-navigation__responsive-container'
				);
				if ( container ) {
					container.classList.remove( 'is-menu-open' );
				}
			} );
		} );

		// Close menu on link click
		const navLinks = document.querySelectorAll(
			'.wp-block-navigation a'
		);
		navLinks.forEach( ( link ) => {
			link.addEventListener( 'click', function() {
				menuContainers.forEach( ( container ) => {
					container.classList.remove( 'is-menu-open' );
				} );
			} );
		} );

		// Close menu on background click
		menuContainers.forEach( ( container ) => {
			container.addEventListener( 'click', function( e ) {
				if ( e.target === this ) {
					this.classList.remove( 'is-menu-open' );
				}
			} );
		} );
	}

	/**
	 * Keyboard Navigation Handler
	 *
	 * Handles escape key to close mobile menu and arrow keys for dropdown navigation
	 */
	function initKeyboardNavigation() {
		document.addEventListener( 'keydown', function( e ) {
			// Close menu on Escape key
			if ( 'Escape' === e.key ) {
				const openContainers = document.querySelectorAll(
					'.wp-block-navigation__responsive-container.is-menu-open'
				);
				openContainers.forEach( ( container ) => {
					container.classList.remove( 'is-menu-open' );
				} );
			}

			// Arrow key navigation in dropdowns
			if ( 'ArrowDown' === e.key || 'ArrowUp' === e.key ) {
				const target = e.target;

				if (
					target.matches(
						'.wp-block-navigation a, .primary-navigation a, .footer-navigation a'
					)
				) {
					e.preventDefault();
					navigateMenuItemsByArrow( target, e.key );
				}
			}
		} );
	}

	/**
	 * Navigate menu items using arrow keys
	 *
	 * @param {HTMLElement} currentLink Current menu link element
	 * @param {string} direction Direction key (ArrowUp or ArrowDown)
	 */
	function navigateMenuItemsByArrow( currentLink, direction ) {
		const listItem = currentLink.closest( 'li' );
		let nextItem = null;

		if ( 'ArrowDown' === direction ) {
			nextItem = listItem.nextElementSibling;
			if ( ! nextItem ) {
				// Wrap to first item
				nextItem = listItem.parentElement.firstElementChild;
			}
		} else if ( 'ArrowUp' === direction ) {
			nextItem = listItem.previousElementSibling;
			if ( ! nextItem ) {
				// Wrap to last item
				nextItem = listItem.parentElement.lastElementChild;
			}
		}

		if ( nextItem ) {
			const link = nextItem.querySelector( 'a' );
			if ( link ) {
				link.focus();
			}
		}
	}

	/**
	 * Active Menu Item Handler
	 *
	 * Updates active menu item indicator based on current page
	 */
	function initActiveMenuItems() {
		const currentLocation = window.location.pathname;
		const menuLinks = document.querySelectorAll(
			'.wp-block-navigation a, .primary-navigation a, .footer-navigation a'
		);

		menuLinks.forEach( ( link ) => {
			const href = link.getAttribute( 'href' );

			if ( href && ( href === currentLocation || href === window.location.href ) ) {
				const listItem = link.closest( 'li' );
				if ( listItem ) {
					listItem.classList.add( 'is-active' );
				}

				// Add to parent li if it's a submenu
				const parentLi = link.closest( 'li li' );
				if ( parentLi ) {
					const grandParentLi = parentLi.closest( 'li' ).parentElement.closest( 'li' );
					if ( grandParentLi ) {
						grandParentLi.classList.add( 'is-active' );
					}
				}
			}
		} );
	}

	/**
	 * Submenu Keyboard Navigation
	 *
	 * Handles enter key to open submenus and escape to close
	 */
	function initSubmenuKeyboardNav() {
		const menuItems = document.querySelectorAll(
			'.wp-block-navigation-item, .primary-navigation li, .footer-navigation li'
		);

		menuItems.forEach( ( item ) => {
			const link = item.querySelector( 'a' );

			if ( link ) {
				link.addEventListener( 'keydown', function( e ) {
					if ( 'Enter' === e.key ) {
						const submenu = item.querySelector(
							'.wp-block-navigation-submenu, ul'
						);

						if ( submenu ) {
							e.preventDefault();

							// Toggle submenu visibility
							if ( submenu.style.display === 'flex' ) {
								submenu.style.display = 'none';
							} else {
								submenu.style.display = 'flex';

								// Focus first submenu link
								const firstLink = submenu.querySelector( 'a' );
								if ( firstLink ) {
									firstLink.focus();
								}
							}
						}
					}
				} );
			}
		} );
	}

	/**
	 * Initialize all navigation handlers
	 */
	function init() {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', function() {
				initMobileMenuToggle();
				initKeyboardNavigation();
				initSubmenuKeyboardNav();
				initActiveMenuItems();
			} );
		} else {
			initMobileMenuToggle();
			initKeyboardNavigation();
			initSubmenuKeyboardNav();
			initActiveMenuItems();
		}
	}

	// Initialize when script loads
	init();

	// Reinitialize on dynamic content load (AJAX)
	document.addEventListener( 'stitch_consulting_navigation_loaded', function() {
		initMobileMenuToggle();
		initKeyboardNavigation();
		initSubmenuKeyboardNav();
		initActiveMenuItems();
	} );

	// Export for use in other scripts
	window.stitchConsultingNavigation = {
		initMobileMenuToggle: initMobileMenuToggle,
		initKeyboardNavigation: initKeyboardNavigation,
		initSubmenuKeyboardNav: initSubmenuKeyboardNav,
		initActiveMenuItems: initActiveMenuItems,
	};
} )();
