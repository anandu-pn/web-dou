document.getElementById('login-form')?.addEventListener('submit', function(event) {
    event.preventDefault();
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;

    alert(`Welcome, ${name}! Your email is ${email}.`);
});
