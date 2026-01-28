// Apply Form
document.getElementById('applyForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const alertBox = document.getElementById('applyAlert');
    alertBox.classList.add('d-none');

    try {
        const res = await fetch('backend/application/process.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();

        if (result.success) {
            alertBox.className = 'alert alert-success';
            e.target.reset();
            setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('applyModal')).hide(), 1500);
        } else {
            alertBox.className = 'alert alert-danger';
        }
        alertBox.textContent = result.message;
        alertBox.classList.remove('d-none');
    } catch (err) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network error.';
        alertBox.classList.remove('d-none');
    }
});

// Login Form
document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const alertBox = document.getElementById('loginAlert');
    alertBox.classList.add('d-none');

    try {
        const res = await fetch('backend/auth/login.php', {
            method: 'POST',
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password')
            }),
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await res.json();

        if (result.success) {
            window.location.href = result.redirect;
        } else {
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = result.message;
            alertBox.classList.remove('d-none');
        }
    } catch (err) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network error.';
        alertBox.classList.remove('d-none');
    }
});

// Contact Form
document.getElementById('contactForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const alertBox = document.getElementById('contactAlert');
    alertBox.classList.add('d-none');

    try {
        const res = await fetch('backend/admin/contact_process.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();

        if (result.success) {
            alertBox.className = 'alert alert-success';
            e.target.reset();
        } else {
            alertBox.className = 'alert alert-danger';
        }
        alertBox.textContent = result.message;
        alertBox.classList.remove('d-none');
    } catch (err) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network error.';
        alertBox.classList.remove('d-none');
    }
});