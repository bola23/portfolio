import { initReveals } from './reveal';
import { initCounters } from './counters';
import { initTilt } from './tilt';
import { initHeroParallax } from './parallax';

/**
 * Lightweight motion layer: no animation library, only CSS transforms driven
 * by IntersectionObserver and pointer events. Skipped for reduced-motion users.
 */
export function initMotion() {
    initReveals();
    initCounters();

    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        initTilt();
        initHeroParallax();
    }

    document.addEventListener('projects:updated', () => initReveals(document.getElementById('project-results') ?? document));
}
