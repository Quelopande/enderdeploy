document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.separate').style.transform = 'translateX(75px)';
    document.querySelector('.sidebar').addEventListener('mouseenter', () => {
        document.querySelector('.separate').style.transform = 'translateX(230px)';
        document.querySelector('.separate').style.marginRight = '200px';
    });
    
    document.querySelector('.sidebar').addEventListener('mouseleave', () => {
        document.querySelector('.separate').style.transform = 'translateX(75px)';
        document.querySelector('.separate').style.marginRight = '45px';
    });
    

    window.sidebarSelected = function sidebarSelected(itemSelectedId) {
        let itemId = document.getElementById(itemSelectedId);
        itemId.classList.add("selected");
    };
    let itemSelectedId = document.body.getAttribute('data-page-id');
    
    if (itemSelectedId) {
        sidebarSelected(itemSelectedId);
    }
});