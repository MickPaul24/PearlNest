/* ════════════════════════════════════════
   PearlNest — Main JavaScript
   Requires: lucide.min.js (loaded before this file)
════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Activate all Lucide icons on the page ──
    if (window.lucide) {
        lucide.createIcons();
    }

    // ── Mobile nav toggle ──
    const navToggle = document.getElementById('navToggle');
    const navLinks  = document.getElementById('navLinks');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', () => navLinks.classList.toggle('open'));
        document.addEventListener('click', e => {
            if (!navToggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('open');
            }
        });
    }

    // ── Admin sidebar mobile toggle ──
    const adminMenuToggle = document.getElementById('adminMenuToggle');
    const adminSidebar    = document.getElementById('adminSidebar');
    if (adminMenuToggle && adminSidebar) {
        adminMenuToggle.addEventListener('click', () => adminSidebar.classList.toggle('open'));
        document.addEventListener('click', e => {
            if (!adminSidebar.contains(e.target) && !adminMenuToggle.contains(e.target)) {
                adminSidebar.classList.remove('open');
            }
        });
    }

    // ── Auto-dismiss alerts after 5 s ──
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity    = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ── Gallery keyboard navigation ──
    const mainImg = document.getElementById('galleryMain');
    const thumbs  = document.querySelectorAll('.gallery-thumb');
    if (mainImg && thumbs.length) {
        let currentIdx = Array.from(thumbs).findIndex(t => t.classList.contains('active'));
        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowRight') { currentIdx = (currentIdx + 1) % thumbs.length; activateThumb(currentIdx); }
            else if (e.key === 'ArrowLeft') { currentIdx = (currentIdx - 1 + thumbs.length) % thumbs.length; activateThumb(currentIdx); }
        });
        function activateThumb(idx) {
            thumbs.forEach(t => t.classList.remove('active'));
            thumbs[idx].classList.add('active');
            mainImg.src = thumbs[idx].src;
        }
    }

    // ── Sort select helper (properties page) ──
    window.applySort = function(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', val);
        window.location.href = url.toString();
    };

    // ── Toast helper ──
    window.showToast = function(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type}`;
        toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;min-width:280px;box-shadow:0 4px 20px rgba(0,0,0,.2);';
        // Re-run createIcons so the icon inside the toast renders
        toast.innerHTML = `<span>${msg}</span>`;
        document.body.appendChild(toast);
        if (window.lucide) lucide.createIcons({ nodes: [toast] });
        setTimeout(() => { toast.style.transition = 'opacity .4s'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 4000);
    };

    // ── Smooth scroll for anchor links ──
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
        });
    });

});
