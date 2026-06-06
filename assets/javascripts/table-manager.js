/**
 * Gestion des tableaux de données : filtrage en temps réel + pagination côté client.
 *
 * Activation : ajouter `data-toolbar` sur le wrapper de la barre d'outils,
 * `data-table-filter` sur l'input de recherche, et `data-page-size` sur le
 * <select> de taille de page. Le tableau doit être un frère suivant du wrapper.
 */

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-table-filter]').forEach(input => {
        const toolbar = input.closest('[data-toolbar]');
        if (!toolbar) return;

        // Cherche le prochain élément frère contenant (ou étant) un <table>
        let tableWrapper = null;
        let table = null;
        let el = toolbar.nextElementSibling;

        while (el) {
            const t = el.tagName === 'TABLE' ? el : el.querySelector('table');
            if (t) { table = t; tableWrapper = el; break; }
            el = el.nextElementSibling;
        }

        if (!table) return;

        const tbody = table.querySelector('tbody');

        if (!tbody) return;

        const pageSizeSelect = toolbar.querySelector('[data-page-size]');
        let pageSize = pageSizeSelect ? parseInt(pageSizeSelect.value, 10) : 20;
        let currentPage = 1;

        // Conteneur de pagination inséré après le wrapper du tableau
        const paginationEl = document.createElement('div');
        paginationEl.className = 'table-pagination d-flex align-items-center justify-content-between mt-2';

        tableWrapper.insertAdjacentElement('afterend', paginationEl);

        /**
         * Supprime les accents d'une chaîne et la met en minuscules,
         * afin de rendre la comparaison insensible à la casse et aux diacritiques (accents).
         *
         * @param {string} str
         * @returns {string}
         */
        function normalize(str) {
            return str.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        }

        /**
         * Retourne toutes les lignes <tr> du <tbody>.
         *
         * @returns {HTMLTableRowElement[]}
         */
        function getAllRows() {
            return Array.from(tbody.querySelectorAll('tr'));
        }

        /**
         * Retourne les lignes dont le contenu correspond à la saisie courante.
         * Si le champ est vide, toutes les lignes sont retournées.
         *
         * @returns {HTMLTableRowElement[]}
         */
        function getFilteredRows() {
            const q = normalize(input.value.trim());

            if (!q) {
                return getAllRows();
            }

            return getAllRows().filter(row => normalize(row.textContent).includes(q));
        }

        /**
         * Masque toutes les lignes puis affiche uniquement celles qui appartiennent
         * à la page courante parmi les résultats filtrés. Met à jour la pagination.
         */
        function render() {
            const all = getAllRows();
            const filtered = getFilteredRows();
            const total = filtered.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            all.forEach(row => { row.hidden = true; });

            const start = (currentPage - 1) * pageSize;
            filtered.slice(start, start + pageSize).forEach(row => { row.hidden = false; });

            const from = total === 0 ? 0 : start + 1;
            const to = Math.min(start + pageSize, total);
            renderPagination(total, totalPages, from, to);
        }

        /**
         * Crée un élément <li> Bootstrap pour la liste de pagination.
         *
         * @param {number}  page     - Numéro de page cible au clic
         * @param {string}  label    - Texte affiché (chiffre, ‹, ›…)
         * @param {boolean} disabled - Désactive le bouton si hors limites
         * @param {boolean} active   - Indique la page courante
         * @returns {HTMLLIElement}
         */
        function makeLi(page, label, disabled, active) {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'page-link';
            btn.innerHTML = label;

            if (disabled) {
                btn.setAttribute('tabindex', '-1');
            } else {
                btn.addEventListener('click', () => { currentPage = page; render(); });
            }

            li.appendChild(btn);

            return li;
        }

        /**
         * Reconstruit la zone de pagination : compteur "X–Y sur Z" à gauche
         * et navigation par pages avec ellipses à droite.
         *
         * @param {number} total      - Nombre total de lignes après filtrage
         * @param {number} totalPages - Nombre total de pages
         * @param {number} from       - Premier index affiché (1-based)
         * @param {number} to         - Dernier index affiché (1-based)
         */
        function renderPagination(total, totalPages, from, to) {
            paginationEl.innerHTML = '';

            const info = document.createElement('span');
            info.className = 'text-meta';
            info.style.fontSize = '.85rem';
            info.textContent = total === 0
                ? 'Aucun résultat'
                : `${from}–${to} sur ${total} résultat${total > 1 ? 's' : ''}`;
            paginationEl.appendChild(info);

            if (totalPages <= 1) return;

            const nav = document.createElement('nav');
            nav.setAttribute('aria-label', 'Pagination');
            const ul = document.createElement('ul');
            ul.className = 'pagination pagination mb-0';

            ul.appendChild(makeLi(currentPage - 1, '‹', currentPage <= 1, false));

            const delta = 2;
            const left  = Math.max(1, currentPage - delta);
            const right = Math.min(totalPages, currentPage + delta);

            if (left > 1) {
                ul.appendChild(makeLi(1, '1', false, false));

                if (left > 2) {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = '<span class="page-link">…</span>';
                    ul.appendChild(li);
                }
            }

            for (let p = left; p <= right; p++) {
                ul.appendChild(makeLi(p, String(p), false, p === currentPage));
            }

            if (right < totalPages) {
                if (right < totalPages - 1) {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = '<span class="page-link">…</span>';
                    ul.appendChild(li);
                }

                ul.appendChild(makeLi(totalPages, String(totalPages), false, false));
            }

            ul.appendChild(makeLi(currentPage + 1, '›', currentPage >= totalPages, false));

            nav.appendChild(ul);
            paginationEl.appendChild(nav);
        }

        input.addEventListener('input', () => { currentPage = 1; render(); });

        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', () => {
                pageSize = parseInt(pageSizeSelect.value, 10);
                currentPage = 1;
                render();
            });
        }

        render();
    });
});
