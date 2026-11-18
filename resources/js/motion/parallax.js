const MAX_ROTATION_DEG = 10;

/**
 * Rotates the hero's 3D scene slightly toward the pointer.
 * The scene reads --scene-rx / --scene-ry; the CSS transition smooths the motion.
 */
export function initHeroParallax() {
    const scene = document.querySelector('[data-hero-scene]');
    const area = scene?.closest('section');

    if (!scene || !area) {
        return;
    }

    let frame = 0;
    let pointerX = 0;
    let pointerY = 0;

    const update = () => {
        frame = 0;
        const rect = area.getBoundingClientRect();
        const x = (pointerX - rect.left) / rect.width - 0.5;
        const y = (pointerY - rect.top) / rect.height - 0.5;

        scene.style.setProperty('--scene-ry', `${(x * MAX_ROTATION_DEG).toFixed(2)}deg`);
        scene.style.setProperty('--scene-rx', `${(-y * MAX_ROTATION_DEG).toFixed(2)}deg`);
    };

    area.addEventListener(
        'pointermove',
        (event) => {
            if (event.pointerType !== 'mouse') {
                return;
            }

            pointerX = event.clientX;
            pointerY = event.clientY;
            frame ||= requestAnimationFrame(update);
        },
        { passive: true },
    );

    area.addEventListener('pointerleave', () => {
        scene.style.removeProperty('--scene-rx');
        scene.style.removeProperty('--scene-ry');
    });
}
