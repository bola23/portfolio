/**
 * Highlights the navigation link of the section currently in view.
 */
export function initActiveNav() {
    const links = document.querySelectorAll('[data-nav-link]');
    const sections = [...links].map((link) => document.getElementById(link.dataset.navLink)).filter(Boolean);

    if (sections.length === 0) {
        return;
    }

    const setActive = (id) => links.forEach((link) => link.toggleAttribute('data-active', link.dataset.navLink === id));

    const observer = new IntersectionObserver(
        (entries) => {
            entries.filter((entry) => entry.isIntersecting).forEach((entry) => setActive(entry.target.id));
        },
        { rootMargin: '-45% 0px -50% 0px' },
    );

    sections.forEach((section) => observer.observe(section));
}
