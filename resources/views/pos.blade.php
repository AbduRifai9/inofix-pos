@extends('layouts.app')

@section('content')
<div class="py-3">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h4">POS System</h2>
                    <button id="new-transaction" class="btn btn-primary">
                        New Transaction
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Product Search and Selection Panel -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" id="search-products" placeholder="Search products..." class="form-control">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Product List -->
                        <div class="row" id="product-list">
                            <!-- Products will be loaded here via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart/Transaction Summary Panel -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h6 mb-0">Transaction Cart</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Customer</label>
                            <select id="customer-select" class="form-select">
                                <option value="">Walk-in Customer</option>
                                <!-- Customer options will be loaded dynamically -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">Items:</span>
                                <span id="items-count">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">Subtotal:</span>
                                <span id="subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span class="fw-medium">Discount:</span>
                                <span id="discount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3 pt-2 border-top border-gray">
                                <span class="fw-bold">Total:</span>
                                <span id="total" class="fw-bold">Rp 0</span>
                            </div>
                        </div>

                        <div class="table-responsive mb-3 max-height-300" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Subtotal</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <!-- Cart items will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button id="clear-cart" class="btn btn-outline-secondary flex-fill">
                                Clear
                            </button>
                            <button id="complete-transaction" class="btn btn-success flex-fill">
                                Complete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Sample products data - in real app, this would come from API
    const products = [
        { id: 1, name: 'Laptop Gaming', code: 'LAP001', price: 12000000, stock: 10 },
        { id: 2, name: 'Smartphone', code: 'SPH001', price: 5000000, stock: 25 },
        { id: 3, name: 'Mouse Wireless', code: 'MSE001', price: 250000, stock: 50 },
        { id: 4, name: 'Keyboard Mechanical', code: 'KBD001', price: 800000, stock: 30 },
        { id: 5, name: 'Monitor 24 inch', code: 'MON001', price: 2000000, stock: 15 },
        { id: 6, name: 'Headphones', code: 'HPH001', price: 600000, stock: 40 },
        { id: 7, name: 'USB Cable', code: 'USB001', price: 75000, stock: 100 },
        { id: 8, name: 'External Hard Drive', code: 'HDD001', price: 1200000, stock: 20 }
    ];

    // Sample customers data - in real app, this would come from API
    const customers = [
        { id: 1, name: 'John Doe', email: 'john@example.com' },
        { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
        { id: 3, name: 'Robert Johnson', email: 'robert@example.com' }
    ];

    // Cart state
    let cart = [];
    let currentTransaction = null;

    // DOM Elements
    const productList = document.getElementById('product-list');
    const cartItems = document.getElementById('cart-items');
    const itemsCount = document.getElementById('items-count');
    const subtotalEl = document.getElementById('subtotal');
    const discountEl = document.getElementById('discount');
    const totalEl = document.getElementById('total');
    const customerSelect = document.getElementById('customer-select');
    const clearCartBtn = document.getElementById('clear-cart');
    const completeTransactionBtn = document.getElementById('complete-transaction');
    const newTransactionBtn = document.getElementById('new-transaction');

    // Initialize the POS interface
    document.addEventListener('DOMContentLoaded', function() {
        loadProducts();
        loadCustomers();
        updateCartDisplay();
    });

    // Load products function
    function loadProducts() {
        productList.innerHTML = '';
        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'col-md-4 col-lg-6 mb-3';
            productCard.innerHTML = `
                <div class="card h-100 shadow-sm border" onclick="addToCart(${product.id})">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text text-muted">Code: ${product.code}</p>
                        <p class="card-text fw-bold text-primary">Rp ${formatRupiah(product.price)}</p>
                        <p class="card-text text-success">Stock: ${product.stock}</p>
                        <button class="btn btn-outline-primary btn-sm w-100 mt-2">Add to Cart</button>
                    </div>
                </div>
            `;
            productList.appendChild(productCard);
        });
    }

    // Load customers function
    function loadCustomers() {
        customerSelect.innerHTML = '<option value="">Walk-in Customer</option>';
        customers.forEach(customer => {
            const option = document.createElement('option');
            option.value = customer.id;
            option.textContent = customer.name;
            customerSelect.appendChild(option);
        });
    }

    // Add to cart function
    function addToCart(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;

        // Check if product is already in cart
        const existingItem = cart.find(item => item.id === productId);
        if (existingItem) {
            // Check stock availability
            if (existingItem.quantity < product.stock) {
                existingItem.quantity++;
            } else {
                alert('Not enough stock available!');
                return;
            }
        } else {
            // Add new item to cart
            if (product.stock > 0) {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1
                });
            } else {
                alert('Product out of stock!');
                return;
            }
        }

        updateCartDisplay();
    }

    // Remove from cart function
    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        updateCartDisplay();
    }

    // Update quantity in cart
    function updateQuantity(productId, change) {
        const item = cart.find(item => item.id === productId);
        if (item) {
            const newQuantity = item.quantity + change;
            const product = products.find(p => p.id === productId);
            
            if (newQuantity > 0 && newQuantity <= product.stock) {
                item.quantity = newQuantity;
            } else if (newQuantity <= 0) {
                cart = cart.filter(i => i.id !== productId);
            }
        }
        updateCartDisplay();
    }

    // Update cart display
    function updateCartDisplay() {
        // Clear cart items
        cartItems.innerHTML = '';

        // Add each cart item
        cart.forEach(item => {
            const product = products.find(p => p.id === item.id);
            const subtotal = item.price * item.quantity;
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <button onclick="updateQuantity(${item.id}, -1)" class="btn btn-sm btn-outline-secondary">-</button>
                        <span class="mx-2">${item.quantity}</span>
                        <button onclick="updateQuantity(${item.id}, 1)" class="btn btn-sm btn-outline-secondary">+</button>
                    </div>
                </td>
                <td>Rp ${formatRupiah(item.price)}</td>
                <td>Rp ${formatRupiah(subtotal)}</td>
                <td>
                    <button onclick="removeFromCart(${item.id})" class="btn btn-sm btn-outline-danger">Remove</button>
                </td>
            `;
            cartItems.appendChild(row);
        });

        // Calculate totals
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = 0;
        
        if (subtotal > 1000000) {
            discount = subtotal * 0.15; // 15% discount
        } else if (subtotal > 500000) {
            discount = subtotal * 0.10; // 10% discount
        }
        
        const total = subtotal - discount;

        // Update display
        itemsCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
        subtotalEl.textContent = `Rp ${formatRupiah(subtotal)}`;
        discountEl.textContent = `Rp ${formatRupiah(discount)}`;
        totalEl.textContent = `Rp ${formatRupiah(total)}`;
    }

    // Format currency function
    function formatRupiah(angka) {
        const reverse = angka.toString().split('').reverse().join('');
        const ribuan = reverse.match(/\d{1,3}/g);
        const hasil = ribuan.join('.').split('').reverse().join('');
        return hasil;
    }

    // Clear cart
    clearCartBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = [];
            updateCartDisplay();
        }
    });

    // Complete transaction
    completeTransactionBtn.addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        // Prepare transaction data
        const transactionData = {
            customer_id: customerSelect.value || null,
            items: cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity
            }))
        };

        // In a real application, you would send this to the API:
        // POST /api/transactions with transactionData

        // For demo purposes, show an alert
        alert('Transaction completed successfully! In a real app, this would be sent to the API.');
        console.log('Transaction data:', transactionData);

        // Reset cart
        cart = [];
        updateCartDisplay();
    });

    // New transaction
    newTransactionBtn.addEventListener('click', function() {
        if (cart.length > 0) {
            if (confirm('Current cart will be cleared. Continue?')) {
                cart = [];
                updateCartDisplay();
            }
        } else {
            updateCartDisplay();
        }
    });

    // Search functionality
    document.getElementById('search-products').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(searchTerm) || 
            product.code.toLowerCase().includes(searchTerm)
        );
        
        productList.innerHTML = '';
        filteredProducts.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'col-md-4 col-lg-6 mb-3';
            productCard.innerHTML = `
                <div class="card h-100 shadow-sm border" onclick="addToCart(${product.id})">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text text-muted">Code: ${product.code}</p>
                        <p class="card-text fw-bold text-primary">Rp ${formatRupiah(product.price)}</p>
                        <p class="card-text text-success">Stock: ${product.stock}</p>
                        <button class="btn btn-outline-primary btn-sm w-100 mt-2">Add to Cart</button>
                    </div>
                </div>
            `;
            productList.appendChild(productCard);
        });
    });
</script>

<style>
    .max-height-300 {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endsection