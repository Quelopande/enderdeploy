window.addEventListener('scroll', function() {
    let scrollPosition = window.scrollY || document.documentElement.scrollTop;
    let nav = document.querySelector('nav');

    if (scrollPosition > 1) {
        nav.style.backgroundColor = 'white';
        nav.style.boxShadow = '0 0 10px -6px';
    } else{
        nav.style.backgroundColor = '#ffffff00';
        nav.style.boxShadow = '0 0 0 0';
    }
});