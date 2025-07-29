// UPDATE JUMLAH PRODUK DI KERANJANG
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function() {
        const productId = this.dataset.id;
        const newQty = this.value;
        
        fetch(`/cart/update.php?id=${productId}&qty=${newQty}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
    });
});

// VALIDASI CHECKOUT
document.querySelector('.checkout-form').addEventListener('submit', function(e) {
    if (document.querySelectorAll('.cart-item').length === 0) {
        e.preventDefault();
        alert("Keranjang kosong!");
    }
});