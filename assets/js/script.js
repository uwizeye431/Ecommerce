document.addEventListener('DOMContentLoaded', () => {
    const notices = document.querySelectorAll('.alert');
    setTimeout(() => notices.forEach(n => n.style.display = 'none'), 3500);

    const baseUrl = document.body.getAttribute('data-base-url') || '';

    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-id');
            const qty = btn.getAttribute('data-qty') || 1;
            const res = await fetch(`${baseUrl}/api/add_to_cart.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: id, quantity: qty })
            });
            const data = await res.json();
            if (!data.success && data.login_url) {
                alert(data.message || 'Please login to add items to cart.');
                window.location.href = data.login_url;
                return;
            }
            alert(data.message || 'Added to cart');
        });
    });
});

