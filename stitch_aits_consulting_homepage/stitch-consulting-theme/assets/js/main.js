/**
 * Stitch Consulting Theme - Main Script
 * Front-end JavaScript for theme functionality
 */

(function() {
	'use strict';

	// Theme object
	const StitchTheme = window.stitchTheme || {};

	/**
	 * Initialize theme on DOM ready
	 */
	document.addEventListener('DOMContentLoaded', function() {
		initDarkMode();
		initMobileMenu();
		initSmoothScroll();
		initLazyLoading();
	});

	/**
	 * Dark mode toggle and support
	 */
	function initDarkMode() {
		// Check for saved dark mode preference
		const savedDarkMode = localStorage.getItem('stitch-dark-mode');
		const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
		const isDarkMode = savedDarkMode !== null ? JSON.parse(savedDarkMode) : prefersDark;

		if (isDarkMode) {
			document.documentElement.classList.add('dark-mode');
		}

		// Listen for changes to system preference
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
			if (localStorage.getItem('stitch-dark-mode') === null) {
				document.documentElement.classList.toggle('dark-mode', e.matches);
			}
		});
	}

	/**
	 * Mobile menu toggle
	 */
	function initMobileMenu() {
		const menuToggle = document.querySelector('.stitch-menu-toggle');
		const mobileMenu = document.querySelector('.stitch-mobile-menu');

		if (menuToggle && mobileMenu) {
			menuToggle.addEventListener('click', function() {
				mobileMenu.classList.toggle('active');
				menuToggle.classList.toggle('active');
				document.body.classList.toggle('menu-open');
			});

			// Close menu when link is clicked
			const menuLinks = mobileMenu.querySelectorAll('a');
			menuLinks.forEach(function(link) {
				link.addEventListener('click', function() {
					mobileMenu.classList.remove('active');
					menuToggle.classList.remove('active');
					document.body.classList.remove('menu-open');
				});
			});
		}
	}

	/**
	 * Smooth scroll behavior
	 */
	function initSmoothScroll() {
		const links = document.querySelectorAll('a[href^="#"]');
		
		links.forEach(function(link) {
			link.addEventListener('click', function(e) {
				const href = this.getAttribute('href');
				const target = document.querySelector(href);
				
				if (target) {
					e.preventDefault();
					target.scrollIntoView({ behavior: 'smooth' });
				}
			});
		});
	}

	/**
	 * Lazy load images
	 */
	function initLazyLoading() {
		if ('IntersectionObserver' in window) {
			const imageObserver = new IntersectionObserver(function(entries, observer) {
				entries.forEach(function(entry) {
					if (entry.isIntersecting) {
						const img = entry.target;
						if (img.dataset.src) {
							img.src = img.dataset.src;
							img.removeAttribute('data-src');
							imageObserver.unobserve(img);
						}
					}
				});
			});

			const lazyImages = document.querySelectorAll('img[data-src]');
			lazyImages.forEach(function(img) {
				imageObserver.observe(img);
			});
		}
	}

	/**
	 * Add scroll event listener for sticky header
	 */
	document.addEventListener('scroll', function() {
		const header = document.querySelector('header');
		if (header && window.scrollY > 100) {
			header.classList.add('scrolled');
		} else if (header) {
			header.classList.remove('scrolled');
		}
	});

	/**
	 * Expose API for external use
	 */
	window.StitchTheme = {
		toggleDarkMode: function() {
			const isDarkMode = !JSON.parse(localStorage.getItem('stitch-dark-mode') || 'false');
			localStorage.setItem('stitch-dark-mode', JSON.stringify(isDarkMode));
			document.documentElement.classList.toggle('dark-mode', isDarkMode);
		},
		version: '1.0.0'
	};

})();
