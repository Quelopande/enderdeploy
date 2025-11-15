const menuBtn = document.getElementById('menuBtn');
const menuBtnIcon = document.getElementById('menuBtnIcon');
const mobileMenu = document.getElementById('mobileMenu');
let nav = document.querySelector('nav');
    
menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    menuBtnIcon.classList.toggle('fa-xmark');
    menuBtnIcon.classList.toggle('fa-bars');
});