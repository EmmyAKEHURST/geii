/**
 * Home Page Interactions
 * 
 * - Navbar scroll effect
 * - Smooth scrolling for anchor links
 * - Card animation on scroll
 */

(function () {
    'use strict';

    /* --------------------------------------------------
       Navbar — classe .navbar-scrolled après 50 px
    -------------------------------------------------- */
    const navbar = document.getElementById('mainNavbar');

    if (navbar) {
        const toggleScrolled = function () {
            navbar.classList.toggle('navbar-scrolled', window.scrollY > 50);
        };
        window.addEventListener('scroll', toggleScrolled, { passive: true });
        toggleScrolled();
    }

    /* --------------------------------------------------
       Défilement fluide pour les ancres internes (#section)
       — ignore href="#" seuls (dropdown Bootstrap « Licences Pro »)
    -------------------------------------------------- */
    document.querySelectorAll('a[href^="#"]').forEach(function (lien) {
        lien.addEventListener('click', function (e) {
            const href = lien.getAttribute('href');
            if (!href || href === '#' || href === '#!') {
                return;
            }
            let cible = null;
            try {
                cible = document.querySelector(href);
            } catch (err) {
                return;
            }
            if (cible) {
                e.preventDefault();
                cible.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* --------------------------------------------------
       Intersection Observer — apparition des cartes au scroll
    -------------------------------------------------- */
    const elementsAnimes = document.querySelectorAll('.card, .espace-card');

    if ('IntersectionObserver' in window && elementsAnimes.length) {

        const observateur = new IntersectionObserver(function (entrées) {
            entrées.forEach(function (entrée) {
                if (entrée.isIntersecting) {
                    entrée.target.classList.add('visible');
                    observateur.unobserve(entrée.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px'
        });

        elementsAnimes.forEach(function (el) {
            observateur.observe(el);
        });

    } else {
        elementsAnimes.forEach(function (el) {
            el.classList.add('visible');
        });
    }

}());
