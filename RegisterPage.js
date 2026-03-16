const username = document.getElementById('username');
const email = document.getElementById('email');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const form = document.getElementById('form');

function setError(element, message){
    const parent = element.parentElement;
    const errorDiv = parent.querySelector('.error');
    if(errorDiv) errorDiv.innerText = message;
    parent.classList.add('error');
    parent.classList.remove('success');
}

function setSuccess(element){
    const parent = element.parentElement;
    const errorDiv = parent.querySelector('.error');
    if(errorDiv) errorDiv.innerText = '';
    parent.classList.add('success');
    parent.classList.remove('error');
}

function isValidEmail(email){
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email.toLowerCase());
}


function isValidPassword(password){
    const re = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
    return re.test(password);
}

function validateInputs(){
    let isValid = true;

    const usernameVal = username.value.trim();
    const emailVal = email.value.trim();
    const passwordVal = password.value.trim();
    const confirmVal = confirmPassword.value.trim();

    if(usernameVal === ''){
        setError(username, 'Username is required!');
        isValid = false;
    } else {
        setSuccess(username);
    }

    if(emailVal === ''){
        setError(email, 'Email is required!');
        isValid = false;
    } else if(!isValidEmail(emailVal)){
        setError(email, 'Email is not valid!');
        isValid = false;
    } else {
        setSuccess(email);
    }

    if(passwordVal === ''){
        setError(password, 'Password is required!');
        isValid = false;
    } else if(!isValidPassword(passwordVal)){
        setError(password, 'Password must be at least 8 chars, 1 uppercase, 1 number');
        isValid = false;
    } else {
        setSuccess(password);
    }

    if(confirmVal === ''){
        setError(confirmPassword, 'Please confirm your password!');
        isValid = false;
    } else if(confirmVal !== passwordVal){
        setError(confirmPassword, 'Passwords do not match!');
        isValid = false;
    } else {
        setSuccess(confirmPassword);
    }

    return isValid;
}

form.addEventListener('submit', function(e){
    if(!validateInputs()){
        e.preventDefault();
    }
});