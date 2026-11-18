/**
 * Calls `onEnter(batch)` once per element when it first becomes visible.
 * IntersectionObserver fires reliably for anchor jumps and fast scrolls, and
 * entries that arrive together are delivered as one batch (for staggering).
 */
export function onFirstView(elements, onEnter, { rootMargin = '0px 0px -8% 0px', threshold = 0.08 } = {}) {
    const targets = Array.from(elements);

    if (targets.length === 0) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            const batch = entries.filter((entry) => entry.isIntersecting).map((entry) => entry.target);

            if (batch.length === 0) {
                return;
            }

            batch.forEach((target) => observer.unobserve(target));
            onEnter(batch);
        },
        { rootMargin, threshold },
    );

    targets.forEach((target) => observer.observe(target));
}
