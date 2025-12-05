/**
 * Navigation & Menu Interactions with WCAG 2.1 AA Accessibility
 *
 * Comprehensive navigation handler providing:
 * - Mobile menu toggle with aria-expanded/aria-hidden state management
 * - Submenu interactions with ARIA attribute synchronization
 * - Keyboard navigation (Escape, Arrow keys, Enter)
 * - Screen reader announcements via aria-live regions
 * - Focus management for keyboard users
 */

( function() {
	'use strict';

	/**
	 * Mobile Menu Toggle Handler with ARIA State Management
	 *
	 * Manages aria-expanded and aria-hidden states for accessibility.
	 * Synchronizes visual state with ARIA attributes for screen readers.
	 */
	function initMobileMenuToggle() {
		const menuButtons = document.querySelectorAll(
			'.wp-block-navigation__responsive-container-open, #primary-menu-toggle'
		);
		const closeButtons = document.querySelectorAll(
			'.wp-block-navigation__responsive-container-close'
		);
		const menuContainers = document.querySelectorAll(
			'.wp-block-navigation__responsive-container'
		);
		const primaryMenu = document.getElementById( 'primary-menu' );
		const primaryToggle = document.getElementById( 'primary-menu-toggle' );

		// Open menu handler
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

				// Update ARIA attributes for accessibility
				if ( this.id === 'primary-menu-toggle' || this === primaryToggle ) {
					if ( primaryToggle && primaryMenu ) {
						primaryToggle.setAttribute( 'aria-expanded', 'true' );
						primaryMenu.setAttribute( 'aria-hidden', 'false' );
						primaryMenu.style.display = 'flex';

						// Announce menu state to screen readers
						announceToScreenReader( 'Primary navigation menu opened' );
					}
				}
			} );
		} );

		// Close menu handler
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
			'.wp-block-navigation a, .primary-navigation a'
		);
		navLinks.forEach( ( link ) => {
			link.addEventListener( 'click', function( e ) {
				// Don't close menu for parent items with submenus
				if ( this.getAttribute( 'aria-haspopup' ) === 'menu' ) {
					e.preventDefault();
					return;
				}

				menuContainers.forEach( ( container ) => {
					container.classList.remove( 'is-menu-open' );
				} );

				// Reset ARIA states for primary menu
				if ( primaryToggle && primaryMenu ) {
					primaryToggle.setAttribute( 'aria-expanded', 'false' );
					primaryMenu.setAttribute( 'aria-hidden', 'true' );
					primaryMenu.style.display = 'none';
				}
			} );
		} );

		// Close menu on background click
		menuContainers.forEach( ( container ) => {
			container.addEventListener( 'click', function( e ) {
				if ( e.target === this ) {
					this.classList.remove( 'is-menu-open' );

					// Reset ARIA states
					if ( primaryToggle && primaryMenu ) {
						primaryToggle.setAttribute( 'aria-expanded', 'false' );
						primaryMenu.setAttribute( 'aria-hidden', 'true' );
						primaryMenu.style.display = 'none';
					}
				}
			} );
		} );
	}

	/**
	 * Submenu Toggle Handler with ARIA State Management
	 *
	 * Handles parent menu items with submenus and manages aria-expanded/aria-hidden.
	 * Synchronizes visibility with accessibility attributes.
	 */
	function initSubmenuToggle() {
		const menuItems = document.querySelectorAll( '[aria-haspopup="menu"]' );

		menuItems.forEach( ( item ) => {
			// Click handler for submenu toggle
			item.addEventListener( 'click', function( e ) {
				// Only handle submenu toggle for parent items
				if ( this.getAttribute( 'aria-haspopup' ) === 'menu' ) {
					e.preventDefault();
					e.stopPropagation();

					const submenu = this.nextElementSibling;

					if ( submenu && submenu.getAttribute( 'role' ) === 'menu' ) {
						// Toggle aria-expanded state
						const isExpanded = this.getAttribute( 'aria-expanded' ) === 'true';
						this.setAttribute( 'aria-expanded', ! isExpanded );

						// Toggle aria-hidden state for submenu
						submenu.setAttribute( 'aria-hidden', isExpanded );

						// Toggle display for visual feedback
						submenu.style.display = isExpanded ? 'none' : 'flex';

						// Announce state change to screen readers
						const label = this.textContent.trim();
						const state = ! isExpanded ? 'expanded' : 'collapsed';
						announceToScreenReader( label + ' submenu ' + state );

						// Focus management: focus first item in submenu if opening
						if ( ! isExpanded ) {
							const firstLink = submenu.querySelector( 'a' );
							if ( firstLink ) {
								setTimeout( function() {
									firstLink.focus();
								}, 50 );
							}
						}
					}
				}
			} );

			// Keyboard handler for submenu (Enter key)
			item.addEventListener( 'keydown', function( e ) {
				if ( 'Enter' === e.key && this.getAttribute( 'aria-haspopup' ) === 'menu' ) {
					e.preventDefault();
					e.stopPropagation();

					const submenu = this.nextElementSibling;
					if ( submenu ) {
						const isExpanded = this.getAttribute( 'aria-expanded' ) === 'true';
						this.setAttribute( 'aria-expanded', ! isExpanded );
						submenu.setAttribute( 'aria-hidden', isExpanded );
						submenu.style.display = isExpanded ? 'none' : 'flex';
					}
				}

				// Escape key to close submenu
				if ( 'Escape' === e.key && this.getAttribute( 'aria-expanded' ) === 'true' ) {
					e.preventDefault();
					this.setAttribute( 'aria-expanded', 'false' );
					const submenu = this.nextElementSibling;
					if ( submenu ) {
						submenu.setAttribute( 'aria-hidden', 'true' );
						submenu.style.display = 'none';
					}

					// Return focus to parent
					this.focus();
					announceToScreenReader( this.textContent.trim() + ' submenu closed' );
				}
			} );
		} );
	}

	/**
	 * Keyboard Navigation Handler
	 *
	 * Handles escape key to close mobile menu and arrow keys for dropdown navigation.
	 * Manages ARIA state for accessibility.
	 */
	function initKeyboardNavigation() {
		document.addEventListener( 'keydown', function( e ) {
			// Close menu on Escape key with ARIA state management
			if ( 'Escape' === e.key ) {
				const openContainers = document.querySelectorAll(
					'.wp-block-navigation__responsive-container.is-menu-open'
				);
				openContainers.forEach( ( container ) => {
					container.classList.remove( 'is-menu-open' );
				} );

				// Also close primary menu and reset ARIA states
				const primaryToggle = document.getElementById( 'primary-menu-toggle' );
				const primaryMenu = document.getElementById( 'primary-menu' );
				if ( primaryToggle && primaryMenu ) {
					primaryToggle.setAttribute( 'aria-expanded', 'false' );
					primaryMenu.setAttribute( 'aria-hidden', 'true' );
					primaryMenu.style.display = 'none';
				}

				// Close all open submenus and reset ARIA
				const openSubmenus = document.querySelectorAll( '[aria-haspopup="menu"][aria-expanded="true"]' );
				openSubmenus.forEach( ( item ) => {
					item.setAttribute( 'aria-expanded', 'false' );
					const submenu = item.nextElementSibling;
					if ( submenu ) {
						submenu.setAttribute( 'aria-hidden', 'true' );
						submenu.style.display = 'none';
					}
				} );

				announceToScreenReader( 'Navigation menu closed' );
			}

			// Arrow key navigation in menus
			if ( 'ArrowDown' === e.key || 'ArrowUp' === e.key ) {
				const target = e.target;

				if (
					target.matches(
						'.wp-block-navigation a, .primary-navigation a, .footer-navigation a, [role="menuitem"]'
					)
				) {
					e.preventDefault();
					navigateMenuItemsByArrow( target, e.key );
				}
			}

			// Tab key to close submenu when tabbing away
			if ( 'Tab' === e.key ) {
				const activeParent = document.activeElement;
				if ( activeParent && activeParent.getAttribute( 'aria-haspopup' ) === 'menu' ) {
					const submenu = activeParent.nextElementSibling;
					if ( submenu && submenu.getAttribute( 'aria-hidden' ) === 'false' ) {
						// Only close if we're tabbing away from the parent
						const nextFocusableElement = getNextFocusable( activeParent, e.shiftKey );
						if ( nextFocusableElement && ! submenu.contains( nextFocusableElement ) ) {
							activeParent.setAttribute( 'aria-expanded', 'false' );
							submenu.setAttribute( 'aria-hidden', 'true' );
							submenu.style.display = 'none';
						}
					}
				}
			}
		} );
	}

	/**
	 * Navigate menu items using arrow keys
	 *
	 * Allows users to navigate menu items with keyboard.
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
	 * Get next focusable element
	 *
	 * @param {HTMLElement} element Current element
	 * @param {boolean} reverse Whether to get previous (shift+tab) or next
	 * @return {HTMLElement|null} Next focusable element
	 */
	function getNextFocusable( element, reverse = false ) {
		const focusableElements = document.querySelectorAll(
			'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
		);
		const focusableArray = Array.from( focusableElements );
		const currentIndex = focusableArray.indexOf( element );

		if ( reverse ) {
			return focusableArray[ currentIndex - 1 ] || null;
		} else {
			return focusableArray[ currentIndex + 1 ] || null;
		}
	}

	/**
	 * Active Menu Item Handler
	 *
	 * Updates active menu item indicator based on current page.
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
					link.setAttribute( 'aria-current', 'page' );
				}

				// Add to parent li if it's a submenu item
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
	 * Handles enter key to open submenus and escape to close.
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
							'.wp-block-navigation-submenu, ul[role="menu"]'
						);

						if ( submenu && this.getAttribute( 'aria-haspopup' ) === 'menu' ) {
							e.preventDefault();
							e.stopPropagation();

							// Toggle submenu visibility
							const isHidden = submenu.getAttribute( 'aria-hidden' ) === 'true';
							this.setAttribute( 'aria-expanded', isHidden );
							submenu.setAttribute( 'aria-hidden', ! isHidden );
							submenu.style.display = isHidden ? 'flex' : 'none';

							if ( isHidden ) {
								// Focus first submenu link
								const firstLink = submenu.querySelector( 'a' );
								if ( firstLink ) {
									setTimeout( function() {
										firstLink.focus();
									}, 50 );
								}

								announceToScreenReader( 'Submenu opened' );
							} else {
								announceToScreenReader( 'Submenu closed' );
							}
						}
					}
				} );
			}
		} );
	}

	/**
	 * Announce message to screen readers
	 *
	 * Uses a live region to announce navigation state changes.
	 *
	 * @param {string} message Message to announce
	 */
	function announceToScreenReader( message ) {
		// Create or get live region
		let liveRegion = document.getElementById( 'stitch-nav-announcer' );
		if ( ! liveRegion ) {
			liveRegion = document.createElement( 'div' );
			liveRegion.id = 'stitch-nav-announcer';
			liveRegion.setAttribute( 'aria-live', 'polite' );
			liveRegion.setAttribute( 'aria-atomic', 'true' );
			liveRegion.style.position = 'absolute';
			liveRegion.style.left = '-10000px';
			liveRegion.style.width = '1px';
			liveRegion.style.height = '1px';
			liveRegion.style.overflow = 'hidden';
			document.body.appendChild( liveRegion );
		}

		// Announce message
		liveRegion.textContent = message;
	}

	/**
	 * Initialize all navigation handlers
	 *
	 * Includes ARIA state management for accessibility.
	 */
	function init() {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', function() {
				initMobileMenuToggle();
				initSubmenuToggle();
				initKeyboardNavigation();
				initSubmenuKeyboardNav();
				initActiveMenuItems();
			} );
		} else {
			initMobileMenuToggle();
			initSubmenuToggle();
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
		initSubmenuToggle();
		initKeyboardNavigation();
		initSubmenuKeyboardNav();
		initActiveMenuItems();
	} );

	// Export for use in other scripts
	window.stitchConsultingNavigation = {
		initMobileMenuToggle: initMobileMenuToggle,
		initSubmenuToggle: initSubmenuToggle,
		initKeyboardNavigation: initKeyboardNavigation,
		initSubmenuKeyboardNav: initSubmenuKeyboardNav,
		initActiveMenuItems: initActiveMenuItems,
		announceToScreenReader: announceToScreenReader
	};
} )();
