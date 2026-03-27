const form = document.getElementById('form');
const name = document.getElementById('name');
const email = document.getElementById('email');
const message1 = document.getElementById('message');

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
    const nameValue = name.value.trim();
    const emailValue = email.value.trim();
    const message1Value = message1.value.trim();
 

    if(nameValue === ''){
        setError(name,'First Name is required!');
    }else{
        setSuccess(name);
    }

    if(emailValue === ''){
        setError(email,'Email is required!');
    }else if(!isValidEmail(emailValue)){
        setError(email,'Provide a valid email address!');
    }else{
        setSuccess(email);
    }

    if(message1Value === ''){
        setError(message1,'Message is required!');
    }else{
        setSuccess(message1);
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "<pre>";
    print_r($_POST);
    exit;
}