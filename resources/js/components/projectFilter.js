/**
 * Filter links are real URLs rendered by the server (shareable and crawlable).
 * With JS, the filtered page is fetched and the results and filters are swapped
 * in place instead of doing a full page load.
 */
export default () => ({
    loading: false,

    init() {
        window.addEventListener('popstate', () => this.load(window.location.href, false));
    },

    navigate(event) {
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.button === 1) {
            return;
        }

        event.preventDefault();
        this.load(event.currentTarget.href, true);
    },

    async load(url, push) {
        this.loading = true;

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

            if (!response.ok) {
                window.location.href = url;
                return;
            }

            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');

            ['#project-results', '[data-filters]'].forEach((selector) => {
                const current = this.$root.querySelector(selector);
                const next = doc.querySelector(selector);

                if (current && next) {
                    current.innerHTML = next.innerHTML;
                }
            });

            if (push) {
                window.history.pushState({}, '', url);
            }

            document.dispatchEvent(new CustomEvent('projects:updated'));
        } catch {
            window.location.href = url;
        } finally {
            this.loading = false;
        }
    },
});
