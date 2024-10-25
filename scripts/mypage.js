document.addEventListener('DOMContentLoaded', function () {
    const avaToggle = document.querySelector('.avatar-toggle');
    const navMypage = document.querySelector('.nav-myPage');
    const overlay = document.querySelector('.overlay');
    const avatar = document.querySelector('.avatar');

   
    avaToggle.addEventListener('click', function () {
        navMypage.classList.toggle('open');
        avatar.classList.toggle('open'); 
    });


    overlay.addEventListener('click', function () {
        navMypage.classList.remove('open');
        avatar.classList.remove('open'); 
    });
});