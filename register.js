// Kontrola, zda se hesla shodují při odeslání formuláře
document.getElementById('registerForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert('Hesla se neshodují, prosím zkuste to znovu.');
        return;
    }

    const formData = new FormData(this);
    try {
        const response = await fetch('register.php', {
            method: 'POST',
            body: formData,
        });

        const result = await response.json();
        alert(result.message);
        if (result.success) {
            window.location.href = 'login.php';
        }
    } catch (error) {
        console.error('Chyba při odesílání formuláře:', error);
        alert('Došlo k chybě při komunikaci se serverem.');
    }
});
