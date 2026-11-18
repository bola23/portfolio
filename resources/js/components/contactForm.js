/**
 * Progressive enhancement for the contact form: submits via fetch, shows
 * validation errors inline and keeps the regular POST as a no-JS fallback.
 */
export default () => ({
    sending: false,
    success: '',
    failure: '',
    errors: {},

    async submit() {
        if (this.sending) {
            return;
        }

        this.sending = true;
        this.success = '';
        this.failure = '';
        this.errors = {};

        try {
            const response = await fetch(this.$el.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(this.$el),
            });

            const payload = await response.json().catch(() => ({}));

            if (response.ok) {
                this.success = payload.message ?? 'Thanks! Your message has been sent.';
                this.$el.reset();
            } else if (response.status === 422) {
                this.errors = payload.errors ?? {};
                this.failure = 'Please check the highlighted fields.';
            } else if (response.status === 429) {
                this.failure = 'Too many messages in a short time. Please try again later or reach out by email.';
            } else if (response.status === 419) {
                this.failure = 'Your session expired. Please refresh the page and try again.';
            } else {
                this.failure = 'Something went wrong. Please try again or reach out by email.';
            }
        } catch {
            this.failure = 'Network error. Please check your connection and try again.';
        } finally {
            this.sending = false;
        }
    },
});
