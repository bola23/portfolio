/**
 * Minimal accessible lightbox for project screenshots.
 */
export default (count) => ({
    active: null,
    lastFocus: null,

    open(index) {
        this.lastFocus = document.activeElement;
        this.active = index;
        document.documentElement.style.overflow = 'hidden';
    },

    close() {
        if (this.active === null) {
            return;
        }

        this.active = null;
        document.documentElement.style.overflow = '';
        this.lastFocus?.focus();
    },

    next() {
        if (this.active !== null) {
            this.active = (this.active + 1) % count;
        }
    },

    prev() {
        if (this.active !== null) {
            this.active = (this.active - 1 + count) % count;
        }
    },
});
