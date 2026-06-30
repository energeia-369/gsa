// Client-side Cart Management System
(function() {
    function getUserCartKey() {
        const userEmail = localStorage.getItem("userEmail") || "guest";
        return `cart_${userEmail}`;
    }

    window.Cart = {
        getItems: function() {
            const key = getUserCartKey();
            const savedCart = localStorage.getItem(key);
            return savedCart ? JSON.parse(savedCart) : [];
        },

        saveItems: function(items) {
            const key = getUserCartKey();
            localStorage.setItem(key, JSON.stringify(items));
            // Trigger storage event for cross-tab or component updates
            window.dispatchEvent(new Event('cartUpdate'));
        },

        addToCart: function(product, quantity = 1) {
            const items = this.getItems();
            const existingProduct = items.find(item => item.id === product.id);

            if (existingProduct) {
                existingProduct.quantity += quantity;
            } else {
                items.push({
                    ...product,
                    quantity: quantity
                });
            }
            this.saveItems(items);
            alert("Product added to cart!");
        },

        removeFromCart: function(id) {
            let items = this.getItems();
            items = items.filter(item => item.id !== id);
            this.saveItems(items);
        },

        updateQuantity: function(id, newQuantity) {
            if (newQuantity < 1) {
                this.removeFromCart(id);
                return;
            }
            const items = this.getItems();
            const product = items.find(item => item.id === id);
            if (product) {
                product.quantity = newQuantity;
            }
            this.saveItems(items);
        },

        clearCart: function(silent = false) {
            if (silent || window.confirm("Are you sure you want to clear your entire cart?")) {
                this.saveItems([]);
            }
        },

        getCartTotal: function() {
            const items = this.getItems();
            return items.reduce((total, item) => total + Number(item.price || 0) * Number(item.quantity || 1), 0);
        },

        getCartCount: function() {
            const items = this.getItems();
            return items.reduce((count, item) => count + item.quantity, 0);
        }
    };
})();
