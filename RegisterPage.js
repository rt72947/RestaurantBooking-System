const form = document.getElementById('form');
const username = document.getElementById('username');
const email = document.getElementById('email');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');

form.addEventListener('submit', (e) => {
    e.preventDefault();

    if(validateInputs()){
        form.submit();
    }
})
const setError = (element, message) => {
    const inputControl = element.parentElement;
    const errorDisplay = inputControl.querySelector('.error');

    errorDisplay.innerText = message;
    inputControl.classList.add('error');
    inputControl.classList.remove('success');

}
const setSuccess = element => {
    const inputControl = element.parentElement;
    const errorDisplay = inputControl.querySelector('.error');

    errorDisplay.innerText = '';
    inputControl.classList.add('success');
    inputControl.classList.remove('error');
}

const isValidEmail = email => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(String(email).toLowerCase());
}
const validateInputs = () => {
    const usernameValue = username.value.trim();
    const emailValue = email.value.trim();
    const passwordValue = password.value.trim();
    const confirmPasswordValue = confirmPassword.value.trim();

    if(usernameValue === ''){
        setError(username,'Username is required!');
    }else{
        setSuccess(username);
    }

    if(emailValue === ''){
        setError(email,'Email is required!');
    }else if(!isValidEmail(emailValue)){
        setError(email,'Provide a valid email address!');
    }else{
        setSuccess(email);
    }

    if(passwordValue === ''){
        setError(password,'Password is required!');
    }else if(passwordValue.length < 8){
        setError(password,'Password must be at least 8 character!');
    }else{
        setSuccess(password);
    }

    if(confirmPasswordValue === ''){
        setError(confirmPassword,'Please confirm your password!');
    }else if(confirmPasswordValue !== passwordValue){
        setError(confirmPassword,"Password doesn't match");
    }else{
        setSuccess(confirmPassword);
    }

}