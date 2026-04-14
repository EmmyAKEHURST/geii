import { Controller } from '@hotwired/stimulus';

/**
 * Provides live password strength feedback based on registration rules.
 */
export default class extends Controller {
    static targets = ['input', 'bar', 'text', 'ruleLength', 'ruleDigit', 'ruleUpper', 'ruleLower', 'ruleSpecial'];

    /**
     * Initializes UI state when the controller is attached to the DOM.
     */
    connect() {
        this.update();
    }

    /**
     * Recalculates password rule matches and refreshes the UI indicators.
     */
    update() {
        const password = this.hasInputTarget ? this.inputTarget.value : '';
        const checks = {
            length: password.length >= 13,
            digit: /[0-9]/.test(password),
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            special: /[^A-Za-z0-9]/.test(password),
        };

        const score = Object.values(checks).filter(Boolean).length;
        const percentage = Math.round((score / 5) * 100);

        this.updateBar(score, percentage);
        this.updateText(score);
        this.updateRule(this.ruleLengthTarget, checks.length);
        this.updateRule(this.ruleDigitTarget, checks.digit);
        this.updateRule(this.ruleUpperTarget, checks.upper);
        this.updateRule(this.ruleLowerTarget, checks.lower);
        this.updateRule(this.ruleSpecialTarget, checks.special);
    }

    /**
     * Updates the progress bar width and semantic color according to the current score.
     */
    updateBar(score, percentage) {
        const classes = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
        this.barTarget.classList.remove(...classes);

        if (score <= 1) {
            this.barTarget.classList.add('bg-danger');
        } else if (score <= 2) {
            this.barTarget.classList.add('bg-warning');
        } else if (score <= 3) {
            this.barTarget.classList.add('bg-info');
        } else {
            this.barTarget.classList.add('bg-success');
        }

        this.barTarget.style.width = `${percentage}%`;
        this.barTarget.parentElement?.setAttribute('aria-valuenow', `${percentage}`);
    }

    /**
     * Updates the human-readable strength label shown below the progress bar.
     */
    updateText(score) {
        let label = 'faible';

        if (score === 2) {
            label = 'moyenne';
        } else if (score === 3) {
            label = 'bonne';
        } else if (score >= 4) {
            label = 'forte';
        }

        this.textTarget.textContent = `Sécurité du mot de passe : ${label}`;
    }

    /**
     * Toggles a visual success marker for one password rule item.
     */
    updateRule(ruleTarget, passed) {
        ruleTarget.classList.toggle('is-valid', passed);
    }
}
