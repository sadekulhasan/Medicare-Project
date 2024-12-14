const bar = document.getElementById('bar');
const close = document.getElementById('close');
const nav = document.getElementById('navbar');

if (bar) {
    bar.addEventListener('click', () => {
        nav.classList.add('active');
    }

    )
}

if (close) {
    close.addEventListener('click', () => {
        nav.classList.remove('active');
    }

    )
}

async function addToCart(productId) {
    try {
        const response = await fetch(`add_to_cart.php?product_id=${productId}`, {
            method: 'GET'
        });
        const result = await response.text();
        if (result === 'success') {
            showNotification("Product added to cart successfully!");
        } else {
            showNotification("Failed to add product to cart.", true);
        }
    } catch (error) {
        console.error("Error:", error);
        showNotification("An error occurred.", true);
    }
}

function showNotification(message, isError = false) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.style.backgroundColor = isError ? '#e63946' : '#4caf50';
    notification.style.display = 'block';
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

