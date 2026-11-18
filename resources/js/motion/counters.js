import { onFirstView } from './inView';

const DURATION_MS = 1600;

const easeOutCubic = (progress) => 1 - (1 - progress) ** 3;

/**
 * Animated counters. The final value is server-rendered, so the number is
 * correct even if this never runs.
 */
export function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');

    onFirstView(counters, (batch) => {
        batch.forEach((element) => {
            const target = Number(element.dataset.counter) || 0;
            const startedAt = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - startedAt) / DURATION_MS, 1);
                element.textContent = Math.round(target * easeOutCubic(progress)).toString();

                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };

            requestAnimationFrame(tick);
        });
    });
}
