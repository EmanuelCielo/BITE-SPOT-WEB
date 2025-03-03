window.addEventListener('scroll', function() {
    const nav = document.querySelector('nav ul');
    
    if (window.scrollY > 10) {
      nav.classList.add('shadow'); // Add shadow when scrolled past 50px
    } else {
      nav.classList.remove('shadow'); // Remove shadow when at the top
    }
  });
  