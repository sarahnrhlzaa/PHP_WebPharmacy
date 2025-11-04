const signUpButton = document.getElementById('signup');
const signInButton = document.getElementById('signin');
const container = document.getElementById('container');

if (signUpButton && signInButton && container) {
    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
}


function togglePasswordSignup(){
    let passwordField = document.querySelector('.sign-up-container input[name="password"]');
    if (passwordField.type === "password"){
        passwordField.type = "text";
    } else{
        passwordField.type = "password";
    }
}

function togglePasswordSignin(){
    let passwordField = document.querySelector('.sign-in-container input[name="password"]');
    if (passwordField.type === "password"){
        passwordField.type = "text";
    } else{
        passwordField.type = "password";
    }
}



// document.getElementById('loginForm').addEventListener('submit', function(e) {
//   const username = document.getElementById('username').value.trim();
//   const password = document.getElementById('password').value.trim();
//   const userError = document.getElementById('userError');
//   const passError = document.getElementById('passError');
//   let valid = true;

//   // Reset pesan error dulu
//   userError.style.display = 'none';
//   passError.style.display = 'none';

//   // Validasi username kosong
//   if (username === "") {
//     userError.textContent = "⚠️ Username cannot be empty!";
//     userError.style.display = 'block';
//     valid = false;
//   }

//   // Validasi password kosong
//   if (password === "") {
//     passError.textContent = "⚠️ Password cannot be empty!";
//     passError.style.display = 'block';
//     valid = false;
//   }

//   // Kalau ada yang kosong, cegah form dikirim
//   if (!valid) {
//     e.preventDefault();
//   }
// });
