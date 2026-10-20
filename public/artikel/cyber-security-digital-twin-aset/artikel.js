// Dipulihkan dari artikel Blade lama (Cyber-Security-Digital-Twin.blade.php).
// Mobile menu toggle - FIXED VERSION
    document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.querySelector('.l-menu-toggle');
      const mainNav = document.querySelector('header nav');
      
      if (menuToggle && mainNav) {
        // Toggle menu when button is clicked
        menuToggle.addEventListener('click', function(e) {
          e.stopPropagation(); // Prevent event bubbling
          mainNav.classList.toggle('active');
          
          // Change icon based on menu state
          const icon = menuToggle.querySelector('i');
          if (mainNav.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
          } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          }
        });
        
        // Close menu when clicking on a link
        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(link => {
          link.addEventListener('click', () => {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
          if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) {
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
            }
          }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
          if (window.innerWidth > 768) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) {
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
            }
          }
        });
      }
      
      // Enhanced animations
      const boxes = document.querySelectorAll('.l-conclusion-box, .l-method-card');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            entry.target.style.transition = 'all 0.6s cubic-bezier(0.22, 0.61, 0.36, 1)';
          }
        });
      }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      });
      
      boxes.forEach(box => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(30px)';
        observer.observe(box);
      });
      
      // Add click effect to conclusion boxes
      document.querySelectorAll('.l-conclusion-box').forEach(box => {
        box.addEventListener('click', function() {
          this.style.transform = 'translateY(-8px)';
          setTimeout(() => {
            this.style.transform = 'translateY(-5px)';
          }, 150);
        });
      });
      
      // Dynamic year in footer
      const yearSpan = document.querySelector('footer p');
      if (yearSpan) {
        yearSpan.innerHTML = yearSpan.innerHTML.replace('2025', new Date().getFullYear());
      }
    });
