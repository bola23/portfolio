const STAGGER_MS = 80;

/**
 * Gentle fade-up for [data-reveal] blocks as they enter the viewport.
 *
 * Only blocks that start below the fold are hidden, so content in the first
 * viewport (and the largest contentful paint) is never delayed by JavaScript.
 * Positions come from IntersectionObserver entries, so nothing forces a
 * synchronous layout. Blocks entering together are staggered.
 */
export function initReveals(root = document) {
    const blocks = root.querySelectorAll('[data-reveal]');

    if (blocks.length === 0) {
        return;
    }

    const pending = new WeakSet();

    const observer = new IntersectionObserver(
        (entries) => {
            const entering = [];

            entries.forEach((entry) => {
                const block = entry.target;

                if (entry.isIntersecting) {
                    observer.unobserve(block);

                    if (pending.has(block)) {
                        entering.push(block);
                    }
                } else if (!pending.has(block) && entry.boundingClientRect.top >= window.innerHeight) {
                    // First report and still below the fold: hide until it scrolls in.
                    pending.add(block);
                    block.classList.add('reveal-pending');
                }
            });

            entering.forEach((block, index) => {
                block.style.setProperty('--reveal-delay', `${index * STAGGER_MS}ms`);
                block.classList.replace('reveal-pending', 'reveal-in');
                block.addEventListener('animationend', () => block.classList.remove('reveal-in'), { once: true });
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );

    blocks.forEach((block) => observer.observe(block));
}
