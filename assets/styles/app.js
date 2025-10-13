// ============================================
// MAIN APPLICATION JAVASCRIPT
// ============================================

// Import styles
import './styles/app.css';

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    initScrollAnimations();
    initCounters();
    initMobileMenu();
    initSmoothScroll();
    initFormValidation();
    initTooltips();
    initImageLazyLoading();
    initSearchFilters();
    initNotifications();
});

// ============================================
// SCROLL ANIMATIONS
// ============================================
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');

                // Trigger counter animation if element has counter class
                if (entry.target.classList.contains('counter')) {
                    animateCounter(entry.target);
                }
            }
        });
    }, observerOptions);

    // Observe all elements with animate-on-scroll class
    document.querySelectorAll('.animate-on-scroll, .animate-fade-in, .animate-slide-up, .animate-slide-right, .animate-slide-left').forEach(el => {
        observer.observe(el);
    });
}

// ============================================
// COUNTER ANIMATIONS
// ============================================
function initCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        counter.textContent = '0';
    });
}

function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000; // 2 seconds
    const step = target / (duration / 16); // 60 FPS
    let current = 0;

    const updateCounter = () => {
        current += step;
        if (current < target) {
            element.textContent = Math.floor(current);
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    };

    updateCounter();
}

// ============================================
// MOBILE MENU
// ============================================
function initMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const menuOverlay = document.querySelector('.menu-overlay');

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            menuOverlay.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        menuOverlay?.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    }

    // Close menu when clicking on a link
    const menuLinks = document.querySelectorAll('.mobile-menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu?.classList.remove('active');
            menuOverlay?.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    });
}

// ============================================
// SMOOTH SCROLL
// ============================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ============================================
// FORM VALIDATION
// ============================================
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
            }
        });

        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });

    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let errorMessage = '';

    // Check if required
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Ce champ est requis';
    }
    // Email validation
    else if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Adresse email invalide';
        }
    }
    // Phone validation
    else if (field.name.includes('phone') && value) {
        const phoneRegex = /^[\d\s+()-]{10,}$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'Numéro de téléphone invalide';
        }
    }
    // Min length validation
    else if (field.hasAttribute('minlength')) {
        const minLength = parseInt(field.getAttribute('minlength'));
        if (value.length < minLength) {
            isValid = false;
            errorMessage = `Minimum ${minLength} caractères requis`;
        }
    }
    // Max length validation
    else if (field.hasAttribute('maxlength')) {
        const maxLength = parseInt(field.getAttribute('maxlength'));
        if (value.length > maxLength) {
            isValid = false;
            errorMessage = `Maximum ${maxLength} caractères autorisés`;
        }
    }

    // Update field UI
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        removeFieldError(field);
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        showFieldError(field, errorMessage);
    }

    return isValid;
}

function showFieldError(field, message) {
    removeFieldError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function removeFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// ============================================
// TOOLTIPS
// ============================================
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            showTooltip(this);
        });

        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function showTooltip(element) {
    const text = element.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.id = 'active-tooltip';

    document.body.appendChild(tooltip);

    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

    setTimeout(() => tooltip.classList.add('visible'), 10);
}

function hideTooltip() {
    const tooltip = document.getElementById('active-tooltip');
    if (tooltip) {
        tooltip.classList.remove('visible');
        setTimeout(() => tooltip.remove(), 200);
    }
}

// ============================================
// LAZY LOADING IMAGES
// ============================================
function initImageLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// ============================================
// SEARCH FILTERS
// ============================================
function initSearchFilters() {
    const searchInput = document.querySelector('.search-filter-input');
    const filterButtons = document.querySelectorAll('.filter-btn');

    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                filterEvents(this.value);
            }, 300);
        });
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const category = this.dataset.category;
            filterByCategory(category);
        });
    });
}

function filterEvents(searchTerm) {
    const events = document.querySelectorAll('.event-card');
    const term = searchTerm.toLowerCase();

    events.forEach(event => {
        const title = event.querySelector('.event-title')?.textContent.toLowerCase();
        const description = event.querySelector('.event-description')?.textContent.toLowerCase();

        if (title?.includes(term) || description?.includes(term)) {
            event.style.display = '';
            event.classList.add('animate-fade-in');
        } else {
            event.style.display = 'none';
        }
    });
}

function filterByCategory(category) {
    const events = document.querySelectorAll('.event-card');

    events.forEach(event => {
        const eventCategory = event.querySelector('.event-category')?.textContent;

        if (category === 'all' || eventCategory === category) {
            event.style.display = '';
            event.classList.add('animate-fade-in');
        } else {
            event.style.display = 'none';
        }
    });
}

// ============================================
// NOTIFICATIONS
// ============================================
function initNotifications() {
    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.classList.add('fade-out');
            setTimeout(() => message.remove(), 300);
        }, 5000);

        // Close button
        const closeBtn = message.querySelector('.flash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                message.classList.add('fade-out');
                setTimeout(() => message.remove(), 300);
            });
        }
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const icon = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type];

    notification.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 10);

    // Auto-hide after 5 seconds
    setTimeout(() => hideNotification(notification), 5000);

    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
        hideNotification(notification);
    });
}

function hideNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => notification.remove(), 300);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format date
function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('fr-FR', options);
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// ============================================
// EXPORT FUNCTIONS FOR GLOBAL USE
// ============================================
window.showNotification = showNotification;
window.formatDate = formatDate;
window.formatCurrency = formatCurrency;

// ============================================
// STICKY HEADER
// ============================================
let lastScroll = 0;
const header = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll <= 0) {
        header?.classList.remove('scroll-up');
        return;
    }

    if (currentScroll > lastScroll && !header?.classList.contains('scroll-down')) {
        // Scrolling down
        header?.classList.remove('scroll-up');
        header?.classList.add('scroll-down');
    } else if (currentScroll < lastScroll && header?.classList.contains('scroll-down')) {
        // Scrolling up
        header?.classList.remove('scroll-down');
        header?.classList.add('scroll-up');
    }

    lastScroll = currentScroll;
});

// ============================================
// BACK TO TOP BUTTON
// ============================================
const backToTopButton = document.querySelector('.back-to-top');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTopButton?.classList.add('visible');
    } else {
        backToTopButton?.classList.remove('visible');
    }
});

backToTopButton?.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// ============================================
// FORM ENHANCEMENTS
// ============================================

// Auto-resize textarea
document.querySelectorAll('textarea[data-auto-resize]').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

// Character counter
document.querySelectorAll('[data-max-chars]').forEach(field => {
    const maxChars = parseInt(field.getAttribute('data-max-chars'));
    const counter = document.createElement('div');
    counter.className = 'char-counter';
    field.parentNode.appendChild(counter);

    function updateCounter() {
        const remaining = maxChars - field.value.length;
        counter.textContent = `${remaining} caractères restants`;
        counter.classList.toggle('warning', remaining < 20);
    }

    field.addEventListener('input', updateCounter);
    updateCounter();
});

// Password strength indicator
document.querySelectorAll('input[type="password"][data-strength]').forEach(passwordField => {
    const strengthBar = document.createElement('div');
    strengthBar.className = 'password-strength';
    strengthBar.innerHTML = '<div class="strength-bar"></div><span class="strength-text"></span>';
    passwordField.parentNode.appendChild(strengthBar);

    passwordField.addEventListener('input', function() {
        const strength = calculatePasswordStrength(this.value);
        const bar = strengthBar.querySelector('.strength-bar');
        const text = strengthBar.querySelector('.strength-text');

        bar.className = 'strength-bar strength-' + strength.level;
        bar.style.width = strength.percentage + '%';
        text.textContent = strength.text;
    });
});

function calculatePasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength += 25;
    if (password.length >= 12) strength += 25;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 20;
    if (/\d/.test(password)) strength += 15;
    if (/[^a-zA-Z0-9]/.test(password)) strength += 15;

    let level, text;
    if (strength < 40) {
        level = 'weak';
        text = 'Faible';
    } else if (strength < 70) {
        level = 'medium';
        text = 'Moyen';
    } else {
        level = 'strong';
        text = 'Fort';
    }

    return { percentage: strength, level, text };
}

console.log('✅ Application JavaScript loaded successfully');
