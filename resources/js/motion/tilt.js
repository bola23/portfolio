const MAX_TILT_DEG = 6;

/**
 * Subtle 3D tilt that follows the pointer over [data-tilt] cards.
 * One delegated listener, at most one style write per frame, mouse/trackpad only.
 */
export function initTilt() {
    let activeCard = null;
    let frame = 0;
    let pointerX = 0;
    let pointerY = 0;

    const reset = (card) => {
        if (card) {
            card.style.removeProperty('transform');
        }
    };

    const update = () => {
        frame = 0;

        if (!activeCard) {
            return;
        }

        const rect = activeCard.getBoundingClientRect();
        const x = (pointerX - rect.left) / rect.width - 0.5;
        const y = (pointerY - rect.top) / rect.height - 0.5;

        activeCard.style.transform = `perspective(900px) rotateX(${(-y * MAX_TILT_DEG).toFixed(2)}deg) rotateY(${(x * MAX_TILT_DEG).toFixed(2)}deg)`;
    };

    document.addEventListener(
        'pointermove',
        (event) => {
            if (event.pointerType !== 'mouse') {
                return;
            }

            const card = event.target instanceof Element ? event.target.closest('[data-tilt]') : null;

            if (card !== activeCard) {
                reset(activeCard);
                activeCard = card;
            }

            if (!card) {
                return;
            }

            pointerX = event.clientX;
            pointerY = event.clientY;
            frame ||= requestAnimationFrame(update);
        },
        { passive: true },
    );

    document.documentElement.addEventListener('pointerleave', () => {
        reset(activeCard);
        activeCard = null;
    });
}
