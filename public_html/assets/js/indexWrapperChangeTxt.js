function changeTxt(buttonId, h2Text, pText) {
    let h2 = document.getElementById('h2TxtBox');
    let p = document.getElementById('pTxtBox');
    let buttons = document.querySelectorAll('.callAppearanceTxt button');

    buttons.forEach(button => {
        button.classList.remove('active');
    });

    document.getElementById(buttonId).classList.add('active');
    h2.innerHTML = h2Text;
    p.innerHTML = pText;
}