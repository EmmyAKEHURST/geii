import { Controller } from '@hotwired/stimulus';

/**
 * Affiche/masque les sections du formulaire d'inscription selon le type de compte choisi.
 * Gère aussi l'attribut `required` sur les champs visibles pour la validation HTML5.
 */
export default class extends Controller {
    static targets = [
        'typeSelect',
        'commonFields',
        'sectionEntreprise',
        'sectionEtudiant',
        'sectionEnseignant',
        'submitBtn',
    ];

    connect() {
        this.switchType();
    }

    switchType() {
        const type = this.typeSelectTarget.value;

        this.#hideAll();

        if (!type) return;

        this.commonFieldsTarget.classList.remove('d-none');
        this.submitBtnTarget.classList.remove('d-none');

        const sectionMap = {
            entreprise: this.sectionEntrepriseTarget,
            etudiant:   this.sectionEtudiantTarget,
            enseignant: this.sectionEnseignantTarget,
        };

        const active = sectionMap[type];
        if (active) {
            active.classList.remove('d-none');
            this.#setRequired(active, true);
        }
    }

    #hideAll() {
        this.commonFieldsTarget.classList.add('d-none');
        this.submitBtnTarget.classList.add('d-none');

        [
            this.sectionEntrepriseTarget,
            this.sectionEtudiantTarget,
            this.sectionEnseignantTarget,
        ].forEach(section => {
            section.classList.add('d-none');
            this.#setRequired(section, false);
        });
    }

    #setRequired(section, required) {
        section.querySelectorAll('input, select, textarea').forEach(el => {
            if (required) {
                el.setAttribute('required', '');
            } else {
                el.removeAttribute('required');
            }
        });
    }
}
