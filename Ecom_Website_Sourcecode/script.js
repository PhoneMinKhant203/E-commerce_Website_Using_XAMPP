document.addEventListener('DOMContentLoaded', () => {
    let menu = document.querySelector('#menu-icon');
    let navigation = document.querySelector('.navigation');

    menu.onclick = () => {
        menu.classList.toggle('bx-x');
        navigation.classList.toggle('open');
    };
});



