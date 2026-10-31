import './styles/tankopedia.sass';

// Submits the Tankopedia filter as soon as a select changes; without JS the
// <noscript> button submits it instead.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-auto-submit]').forEach((form) => {
        form.addEventListener('change', () => form.requestSubmit());
    });
});
