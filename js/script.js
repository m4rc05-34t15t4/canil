document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
  
    // Toggle da navegação em dispositivos móveis
    hamburger.addEventListener('click', function() {
      navLinks.classList.toggle('active');
    });
  });
  