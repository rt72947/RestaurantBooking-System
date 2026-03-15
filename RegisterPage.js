const form = document.getElementById('form');
const username = document.getElementById('username');
const email = document.getElementById('email');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');

form.addEventListener('submit', (e) => {
    if(!validateInputs()){
        e.preventDefault();
    }
});

function setError(element, message){
    const parent = element.parentElement;
    const errorDiv = parent.querySelector('.error');
    errorDiv.innerText = message;
    parent.classList.add('error');
    parent.classList.remove('success');
}

function setSuccess(element){
    const parent = element.parentElement;
    const errorDiv = parent.querySelector('.error');
    errorDiv.innerText = '';
    parent.classList.add('success');
    parent.classList.remove('error');
}

function isValidEmail(email){
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email.toLowerCase());
}

function validateInputs(){
    let valid = true;

    const usernameVal = username.value.trim();
    const emailVal = email.value.trim();
    const passwordVal = password.value.trim();
    const confirmVal = confirmPassword.value.trim();

    if(usernameVal === ''){
        setError(username, 'Username is required!');
        valid = false;
    } else setSuccess(username);

    if(emailVal === ''){
        setError(email, 'Email is required!');
        valid = false;
    } else if(!isValidEmail(emailVal)){
        setError(email, 'Email is not valid!');
        valid = false;
    } else setSuccess(email);

    if(passwordVal === ''){
        setError(password, 'Password is required!');
        valid = false;
    } else if(passwordVal.length < 8){
        setError(password, 'Password must be at least 8 characters!');
        valid = false;
    } else setSuccess(password);

    if(confirmVal === ''){
        setError(confirmPassword, 'Please confirm password!');
        valid = false;
    } else if(confirmVal !== passwordVal){
        setError(confirmPassword, 'Passwords do not match!');
        valid = false;
    } else setSuccess(confirmPassword);

    return valid;
}