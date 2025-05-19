document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const navbarLinks = document.getElementById('navbar-links');
    
    menuToggle.addEventListener('click', () => {
        navbarLinks.classList.toggle('active');
    });
    
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                navbarLinks.classList.remove('active');
            }
        });
    });
});