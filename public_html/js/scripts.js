document.addEventListener('DOMContentLoaded', function () {

    // --- Debounce Function for Performance ---
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // --- Navbar Scroll Effect (Debounced) ---
    const mainNav = document.getElementById('mainNav');
    if (mainNav) {
        const handleNavbarEffect = () => {
            if (window.innerWidth < 992) {
                // On mobile, always remove the 'scrolled' class and do nothing else
                mainNav.classList.remove('scrolled');
            } else {
                // On desktop, handle the scroll effect
                if (window.scrollY > 50) {
                    mainNav.classList.add('scrolled');
                } else {
                    mainNav.classList.remove('scrolled');
                }
            }
        };

        // Listen for both scroll and resize events
        window.addEventListener('scroll', debounce(handleNavbarEffect, 15));
        window.addEventListener('resize', debounce(handleNavbarEffect, 15));
        
        // Initial check on page load
        handleNavbarEffect();
    }

    // --- AOS Initialization ---
    AOS.init({
        duration: 800,
        once: true,
    });

    // --- Stat Counter Animation ---
    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            let current = Math.floor(progress * (end - start) + start);
            
            // Use the suffix from the data attribute directly
            const suffix = obj.dataset.suffix || '';
            obj.innerHTML = current + suffix;
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    const statCards = document.querySelectorAll('.stat-card h2[data-count]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const count = parseInt(el.getAttribute('data-count'), 10);
                
                // Simplified suffix detection
                if(el.innerHTML.includes('%')) el.dataset.suffix = '%';
                if(el.innerHTML.includes('/7')) el.dataset.suffix = '/7';

                animateValue(el, 0, count, 1500);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    statCards.forEach(card => {
        observer.observe(card);
    });

    // Easing function for smooth scroll
    function easeInOutCubic(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t * t + b;
        t -= 2;
        return c / 2 * (t * t * t + 2) + b;
    };

    // Custom smooth scroll implementation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                console.log('Custom smooth scrolling to:', targetId);
                const startPosition = window.pageYOffset;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                const distance = targetPosition - startPosition;
                const duration = 800; // milliseconds
                let start = null;

                window.requestAnimationFrame(function step(timestamp) {
                    if (!start) start = timestamp;
                    const progress = timestamp - start;
                    window.scrollTo(0, easeInOutCubic(progress, startPosition, distance, duration));
                    if (progress < duration) {
                        window.requestAnimationFrame(step);
                    }
                });
            } else {
                console.log('Target element not found:', targetId);
            }
        });
    });

    // Custom smooth scroll is removed as `scroll-behavior: smooth;` in CSS handles this natively.
});