// Dipulihkan dari artikel Blade lama (rancang-bangun-agent-router-sub-agent.blade.php).
document.addEventListener('DOMContentLoaded', function () {
      const menuToggle = document.querySelector('.l-menu-toggle');
      const mainNav = document.querySelector('header nav');
      if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function (e) {
          e.stopPropagation();
          mainNav.classList.toggle('active');
          const icon = menuToggle.querySelector('i');
          icon.classList.contains('fa-bars') ? icon.classList.replace('fa-bars','fa-times') : icon.classList.replace('fa-times','fa-bars');
        });
        mainNav.querySelectorAll('a').forEach(link => {
          link.addEventListener('click', () => {
            mainNav.classList.remove('active');
            menuToggle.querySelector('i').classList.replace('fa-times','fa-bars');
          });
        });
        document.addEventListener('click', function (e) {
          if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) icon.classList.replace('fa-times','fa-bars');
          }
        });
        window.addEventListener('resize', function () {
          if (window.innerWidth > 768) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) icon.classList.replace('fa-times','fa-bars');
          }
        });
      }

      const boxes = document.querySelectorAll('.l-conclusion-box, .l-method-card, .l-domain-card, .l-token-stat');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            entry.target.style.transition = 'all 0.6s cubic-bezier(0.22,0.61,0.36,1)';
          }
        });
      }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
      boxes.forEach(box => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(30px)';
        observer.observe(box);
      });

      document.querySelectorAll('.l-conclusion-box').forEach(box => {
        box.addEventListener('click', function () {
          this.style.transform = 'translateY(-8px)';
          setTimeout(() => { this.style.transform = 'translateY(-5px)'; }, 150);
        });
      });

      (document.getElementById('footerYear')||{}).textContent = new Date().getFullYear();
    });
