function menu(action) {
    const menu = document.querySelector('.ullast');
    const nav = document.querySelector('nav');
    const faBars = document.querySelector('.fa-bars');
    const faX = document.querySelector('.fa-xmark');

    if (action === "open") {
        menu.setAttribute("style", "display: block !important;");
        nav.setAttribute("style", "border-radius: 30px !important; background-color: white !important;padding-bottom:10px;");
        faBars.setAttribute("style", "display: none !important;");
        faX.setAttribute("style", "display: block !important;");
    } else if (action === "close") {
        menu.setAttribute("style", "display: none !important;");
        nav.setAttribute("style", "border-radius: 100px; background-color: white;margin: 5px 20px;");
        faBars.setAttribute("style", "display: block !important;");
        faX.setAttribute("style", "display: none !important;");
    }
}