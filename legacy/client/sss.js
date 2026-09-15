// Retrieve the cart data from localStorage
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Function to display cart items
function displayCartItems() {
    const cartItems = document.getElementById('cart-items');
    cartItems.innerHTML = ''; // Clear previous items

    let totalPrice = 0;
    cart.forEach(item => {
        const li = document.createElement('li');
        li.textContent = `${item.name} - ${item.price} جنيه`;
        cartItems.appendChild(li);
        totalPrice += item.price;
    });

    // Update total price
    document.getElementById('total-price').textContent = `إجمالي السعر: ${totalPrice} جنيه`;
}

// Call the function to display items
