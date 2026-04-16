/**
 * Home Page Interactions
 *
 * - Navbar scroll effect
 * - Smooth scrolling for anchor links
 * - Card animation on scroll
 */

(function () {
    'use strict';

    const navbar = document.getElementById('mainNavbar');
    const isHomeNavbar = navbar instanceof HTMLElement && navbar.classList.contains('navbar-home');
    const navbarScrollThreshold = navbar instanceof HTMLElement
        ? Number.parseInt(navbar.dataset.scrollThreshold || '800', 10)
        : 800;
    const animatedElements = document.querySelectorAll('.card, .espace-card');

    /**
     * Toggles the home navbar state after the configured scroll threshold.
     *
     * @returns {void}
     */
    function toggleScrolledNavbar() {
        if (!navbar || !isHomeNavbar) {
            return;
        }

        navbar.classList.toggle('navbar-scrolled', window.scrollY >= navbarScrollThreshold);
    }

    /**
     * Registers navbar scroll listeners and applies initial state immediately.
     *
     * @returns {void}
     */
    function initNavbarScrollEffect() {
        if (!navbar) {
            return;
        }

        if (!isHomeNavbar) {
            return;
        }

        window.addEventListener('scroll', toggleScrolledNavbar, { passive: true });
        toggleScrolledNavbar();
    }

    /**
     * Resolves a safe DOM target from an anchor href.
     *
     * @param {string | null} href - Link href value.
     * @returns {Element | null}
     */
    function resolveAnchorTarget(href) {
        if (!href || href === '#' || href === '#!') {
            return null;
        }

        try {
            return document.querySelector(href);
        } catch {
            return null;
        }
    }

    /**
     * Smooth-scrolls to a target section when a valid internal anchor is clicked.
     *
     * @param {MouseEvent} event - Click event dispatched by an internal anchor link.
     * @returns {void}
     */
    function handleInternalAnchorClick(event) {
        const link = event.currentTarget;
        if (!(link instanceof HTMLAnchorElement)) {
            return;
        }

        const target = resolveAnchorTarget(link.getAttribute('href'));
        if (target) {
            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    /**
     * Attaches smooth scrolling behavior to internal anchor links.
     *
     * @returns {void}
     */
    function initSmoothScrollForAnchors() {
        document.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', handleInternalAnchorClick);
        });
    }

    /**
     * Reveals one observed element and stops observing it once visible.
     *
     * @param {IntersectionObserverEntry} entry - Current observer entry.
     * @param {IntersectionObserver} observer - Observer managing the target.
     * @returns {void}
     */
    function revealObservedElement(entry, observer) {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    }

    /**
     * Initializes card and panel reveal animations using IntersectionObserver
     * with a no-JS-fallback that reveals all elements immediately.
     *
     * @returns {void}
     */
    function initCardAnimations() {
        if (!animatedElements.length) {
            return;
        }

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    revealObservedElement(entry, observer);
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px'
            });

            animatedElements.forEach(function (element) {
                observer.observe(element);
            });

            return;
        }

        animatedElements.forEach(function (element) {
            element.classList.add('visible');
        });
    }

    initNavbarScrollEffect();
    initSmoothScrollForAnchors();
    initCardAnimations();
}());
