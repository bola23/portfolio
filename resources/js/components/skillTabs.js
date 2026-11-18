import { prefersReducedMotion } from '../utils/media';

/**
 * Category tabs for the skills grid.
 *
 * Every tab button uses the same directive strings (the category comes from
 * `data-tab`) and the cards are filtered here instead of with one `x-show`
 * per card, so Alpine compiles a handful of expressions instead of dozens.
 */
export default () => ({
    tab: 'all',

    select(tab) {
        this.tab = tab;

        this.$refs.grid.querySelectorAll('[data-category]').forEach((card) => {
            const isVisible = tab === 'all' || card.dataset.category === tab;
            const wasHidden = card.hidden;
            card.hidden = !isVisible;

            if (isVisible && wasHidden && !prefersReducedMotion()) {
                card.animate([{ opacity: 0, transform: 'scale(0.96)' }, { opacity: 1, transform: 'none' }], {
                    duration: 250,
                    easing: 'ease-out',
                });
            }
        });
    },

    isActive(tab) {
        return this.tab === tab;
    },
});
