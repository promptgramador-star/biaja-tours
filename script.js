document.addEventListener('DOMContentLoaded', () => {
    // Standard Interactions
    const header = document.querySelector('.header');

    // Sticky Header
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Mobile Menu
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            // Optional: Toggle icon between bars and times (X)
            const icon = mobileToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // Close menu when clicking a link
    // Close menu when clicking ANY link in the menu (including buttons)
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });

    // Smooth Scroll for Anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Intersection Observer for Fade-in animations
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Elements to animate
    const animElements = document.querySelectorAll('.card, .section-header, .corporate-text, .service-card');

    // Only observe if elements exist
    if (animElements.length > 0) {
        animElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(el);
        });
    }

    // Add visible class styling dynamically
    const style = document.createElement('style');
    style.innerHTML = `
        .visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);

    // --- Internationalization (i18n) ---
    // --- Internationalization (i18n) ---
    const langOpts = document.querySelectorAll('.lang-opt');

    // Default language: Spanish or saved preference
    let currentLang = localStorage.getItem('biaja_lang') || 'es';

    function updateLanguage(lang) {
        // Update variable
        currentLang = lang;
        localStorage.setItem('biaja_lang', lang);

        // Update Toggle UI
        langOpts.forEach(opt => {
            const optLang = opt.getAttribute('data-lang');
            if (optLang === lang) {
                opt.classList.add('active');
            } else {
                opt.classList.remove('active');
            }
        });

        // Update Text Context
        if (typeof translations !== 'undefined') {
            console.log('Translations loaded for:', lang);
            const t = translations[lang];
            if (!t) {
                console.error('Translation key not found for lang:', lang);
                return;
            }

            // textContent updates
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (t[key]) {
                    // If it contains HTML tag (like <br> or span), use innerHTML
                    if (t[key].includes('<')) {
                        el.innerHTML = t[key];
                    } else {
                        el.textContent = t[key];
                    }
                } else {
                    console.warn('Missing translation for key:', key);
                }
            });

            // placeholder updates
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                const key = el.getAttribute('data-i18n-placeholder');
                if (t[key]) {
                    el.placeholder = t[key];
                }
            });
        } else {
            console.error('Translations object is undefined!');
        }
    }

    // Initialize
    updateLanguage(currentLang);

    // Event Listeners for Simple Toggle
    langOpts.forEach(opt => {
        opt.addEventListener('click', () => {
            const selectedLang = opt.getAttribute('data-lang');
            updateLanguage(selectedLang);
        });
    });
});

// --- Payment Logic (Global Access) ---
async function reservar(paqueteId) {
    console.log('Iniciando reserva para:', paqueteId);

    // UI Feedback (Optional: show loader on button)
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';
    btn.disabled = true;

    try {
        const response = await fetch('/api/crear-sesion-pago', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                package_id: paqueteId,
                currency: 'USD',
                timestamp: new Date().toISOString()
            })
        });

        if (!response.ok) {
            // Mocking success for demo purposes if backend fails (since it doesn't exist yet)
            console.warn('Backend not found, mocking redirect...');
            // throw new Error('Error en la pasarela de pago'); // In real prod, throw.

            // DEMO SIMULATION
            setTimeout(() => {
                alert(`[DEMO] Redirigiendo a pasarela de pago para el paquete: ${paqueteId}`);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 1500);
            return;
        }

        const data = await response.json();

        if (data.redirect_url) {
            window.location.href = data.redirect_url;
        } else {
            alert('Error: No se recibió URL de pago');
        }

    } catch (error) {
        console.error('Error al reservar:', error);

        // Fallback for Demo
        setTimeout(() => {
            alert(`[DEMO] Redirigiendo a pasarela de pago para el paquete: ${paqueteId}\n(Backend API not reachable)`);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 1500);
    }
}
