import { startStimulusApp } from '@symfony/stimulus-bundle';
import PasswordStrengthController from './controllers/password_strength_controller.js';

/**
 * Starts the global Stimulus application and registers custom controllers.
 */
const app = startStimulusApp();

/**
 * Handles live password strength feedback on registration forms.
 */
app.register('password-strength', PasswordStrengthController);
