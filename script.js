const signinBtn = document.querySelector('.signinBtn');
const signupBtn = document.querySelector('.signupBtn');
const formBx = document.querySelector('.formBx');
const body = document.body
const bluebg = document.querySelector('.blueBg');
console.log(bluebg);
signupBtn.onclick = function () {
    formBx.classList.add('active');
    body.classList.add('active');
    bluebg.classList.add('active');
}
signinBtn.onclick = function () {
    formBx.classList.remove('active');
    body.classList.remove('active');
    bluebg.classList.remove('active');
}




