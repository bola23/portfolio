import Alpine from 'alpinejs';
import contactForm from './components/contactForm';
import projectFilter from './components/projectFilter';
import gallery from './components/gallery';
import skillTabs from './components/skillTabs';
import { prefersReducedMotion } from './utils/media';
import { initActiveNav } from './components/activeNav';
import { initMotion } from './motion';

Alpine.data('contactForm', contactForm);
Alpine.data('projectFilter', projectFilter);
Alpine.data('gallery', gallery);
Alpine.data('skillTabs', skillTabs);

window.Alpine = Alpine;
Alpine.start();

initActiveNav();

// Scroll reveals, counters and 3D pointer effects are skipped for reduced-motion users.
if (!prefersReducedMotion()) {
    initMotion();
}
