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
                            <div class="product-search-container position-relative mb-3">
                                <input type="text" id="search-products" placeholder="Cari produk..."
                                    class="form-control">
                                <div class="dropdown-menu dropdown-menu-end w-100" id="product-dropdown"
                                    style="display: none; position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                </div>
                            </div>

                            <!-- Product List -->
                            <div class="row" id="product-list">
                                @forelse($products as $product)
                                    <div class="col-md-4 col-lg-3 mb-3">
                                        <div class="card h-100 shadow-sm border">
                                            @if (isset($product['image_url']))
                                                <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}"
                                                    class="card-img-top" style="height: 150px; object-fit: cover;">
                                            @endif
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $product['name'] }}</h5>
                                                <p class="card-text text-muted small">Code: {{ $product['code'] }}</p>
                                                <p class="card-text fw-bold text-primary">Rp
                                                    {{ number_format($product['price'], 0, '.', '.') }}</p>
                                                <p class="card-text text-success">Stock: {{ $product['stock'] }}</p>
                                            </div>
                                            <div class="card-footer">
                                                <button class="btn btn-primary btn-sm w-100"
                                                    onclick="addToCart({{ $product['id'] }})">
                                                    Tambah ke Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-center text-muted">No products found</p>
                                    </div>
                                @endforelse
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
                        <!-- Notification area -->
                        <div id="notification-area" class="alert d-none" role="alert" style="margin: 10px;">
                            <span id="notification-message"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="float: right;"></button>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Pelanggan</label>
                                <div class="position-relative">
                                    <div class="input-group">
                                        <input type="text" id="customer-search" placeholder="Cari atau pilih pelanggan..."
                                            class="form-control" autocomplete="off">
                                        <button class="btn btn-outline-primary" type="button" id="add-customer-btn"
                                            data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-end w-100" id="customer-dropdown"
                                        style="display: none; position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
                                </div>
                                <input type="hidden" id="customer-id" value="">
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
                                <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                    <span class="fw-bold">Total:</span>
                                    <span id="total" class="fw-bold">Rp 0</span>
                                </div>
                            </div>

                            <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm">
                                    <thead>
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

    <!-- Hidden input to store the API token -->
    <input type="hidden" id="api-token" value="{{ $api_token }}">

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelanggan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-customer-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customer-name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="customer-name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="customer-phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="customer-phone" name="phone">
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="customer-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customer-email" name="email">
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Cart state
        let cart = [];
        let allProducts = @json($products);
        let allCustomers = @json($customers);

        // DOM Elements
        const productList = document.getElementById('product-list');
        const cartItems = document.getElementById('cart-items');
        const itemsCount = document.getElementById('items-count');
        const subtotalEl = document.getElementById('subtotal');
        const discountEl = document.getElementById('discount');
        const totalEl = document.getElementById('total');
        const customerSelect = document.getElementById('customer-id');
        const clearCartBtn = document.getElementById('clear-cart');
        const completeTransactionBtn = document.getElementById('complete-transaction');
        const newTransactionBtn = document.getElementById('new-transaction');
        const searchProductsInput = document.getElementById('search-products');
        const apiToken = document.getElementById('api-token').value;

        // Initialize the POS interface
        document.addEventListener('DOMContentLoaded', function() {
            updateCartDisplay();

            // Event listeners
            clearCartBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus keranjang?')) {
                    cart = [];
                    updateCartDisplay();
                }
            });

            completeTransactionBtn.addEventListener('click', completeTransaction);
            newTransactionBtn.addEventListener('click', function() {
                if (cart.length > 0) {
                    if (confirm('Keranjang saat ini akan dihapus. Lanjutkan?')) {
                        cart = [];
                        updateCartDisplay();
                    }
                } else {
                    updateCartDisplay();
                }
            });

            searchProductsInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const filteredProducts = allProducts.filter(product =>
                    product.name.toLowerCase().includes(searchTerm) ||
                    product.code.toLowerCase().includes(searchTerm)
                );

                displayProducts(filteredProducts);
            });
        });

        // Display products in the product list
        function displayProducts(products) {
            let html = '';

            if (products.length === 0) {
                html = `
            <div class="col-12">
                <p class="text-center text-muted">No products found</p>
            </div>`;
            } else {
                products.forEach(product => {
                    html += `
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card h-100 shadow-sm border">
                    <img src="${product.image_url ?? ''}" class="card-img-top" style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="text-muted small">Code: ${product.code}</p>
                        <p class="fw-bold text-primary">Rp ${formatRupiah(product.price)}</p>
                        <p class="text-success">Stock: ${product.stock}</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-sm w-100" onclick="addToCart(${product.id})">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </div>`;
                });
            }

            productList.innerHTML = html;
        }

        // Add to cart function with Indonesian messages
        function addToCart(productId) {
            const product = allProducts.find(p => p.id === productId);
            if (!product) {
                // Produk tidak ditemukan, bisa ditangani dengan cara lain jika diperlukan
                return;
            }

            // Check if product is already in cart
            const existingItem = cart.find(item => item.id === productId);
            if (existingItem) {
                // Check stock availability
                if (existingItem.quantity < product.stock) {
                    existingItem.quantity++;
                } else {
                    // Stok tidak mencukupi, bisa ditangani dengan cara lain jika diperlukan
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
                    // Produk habis stok, bisa ditangani dengan cara lain jika diperlukan
                    return;
                }
            }

            updateCartDisplay();
        }

        // Remove from cart function
        function removeFromCart(productId) {
            const product = allProducts.find(p => p.id === productId);
            if (confirm(`Apakah Anda yakin ingin menghapus ${product.name} dari keranjang?`)) {
                cart = cart.filter(item => item.id !== productId);
                updateCartDisplay();
            }
        }

        // Update quantity in cart
        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                const newQuantity = item.quantity + change;
                const product = allProducts.find(p => p.id === productId);

                if (newQuantity > 0 && newQuantity <= product.stock) {
                    item.quantity = newQuantity;
                } else if (newQuantity <= 0) {
                    if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
                        cart = cart.filter(i => i.id !== productId);
                    }
                    return;
                } else {
                    // Tidak dapat menambahkan lebih dari stok yang tersedia, bisa ditangani dengan cara lain jika diperlukan
                    return;
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
                const product = allProducts.find(p => p.id === item.id);
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
                    <button onclick="removeFromCart(${item.id})" class="btn btn-sm btn-outline-danger">Hapus</button>
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
            if (typeof angka !== 'number') {
                angka = parseFloat(angka) || 0;
            }

            const reverse = angka.toString().split('').reverse().join('');
            const ribuan = reverse.match(/\d{1,3}/g);
            const hasil = ribuan.join('.').split('').reverse().join('');
            return hasil;
        }

        // Show notification function
        function showNotification(message, type = 'success') {
            const notificationArea = document.getElementById('notification-area');
            const notificationMessage = document.getElementById('notification-message');
            
            notificationMessage.textContent = message;
            notificationArea.className = `alert alert-${type} d-block`;
            
            // Auto hide the notification after 3 seconds
            setTimeout(() => {
                notificationArea.classList.add('d-none');
            }, 3000);
        }

        // Prevent multiple simultaneous transactions
        let isProcessingTransaction = false;

        // Complete transaction
        function completeTransaction() {
            if (cart.length === 0) {
                // Keranjang kosong, bisa ditangani dengan cara lain jika diperlukan
                showNotification('Keranjang kosong, silakan tambahkan produk terlebih dahulu!', 'warning');
                return;
            }

            // Prevent multiple clicks during processing
            if (isProcessingTransaction) {
                showNotification('Transaksi sedang diproses, mohon tunggu...', 'info');
                return;
            }

            isProcessingTransaction = true;
            completeTransactionBtn.disabled = true;
            completeTransactionBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

            // Prepare transaction data
            const transactionData = {
                customer_id: document.getElementById('customer-id').value || null, // Use value from hidden input
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity
                }))
            };

            // Send transaction to API
            fetch('/api/transactions', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + apiToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(transactionData)
                })
                .then(async response => {
                    if (response.status === 401) {
                        // Sesi habis, redirect ke login
                        window.location.href = '/login';
                        return;
                    }
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Kesalahan jaringan');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.message) {
                        console.log('Respon transaksi:', data);
                        
                        // Show success notification
                        showNotification('Transaksi berhasil diselesaikan!', 'success');

                        // Reset cart only after successful response
                        cart = [];
                        updateCartDisplay();
                        
                        // Reset customer selection
                        document.getElementById('customer-search').value = '';
                        document.getElementById('customer-id').value = '';
                    } else {
                        // Show error notification for failed transaction
                        showNotification('Gagal menyelesaikan transaksi: ' + (data.message || 'Terjadi kesalahan'), 'danger');
                    }
                })
                .catch(error => {
                    console.error('Kesalahan:', error);
                    // Show error notification for transaction errors
                    showNotification('Terjadi kesalahan saat menyelesaikan transaksi: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Always reset processing state and button
                    isProcessingTransaction = false;
                    completeTransactionBtn.disabled = false;
                    completeTransactionBtn.textContent = 'Complete';
                });
        }

        // Initialize allCustomers variable with the data from the view


        // Customer search and selection functionality
        const customerSearch = document.getElementById('customer-search');
        const customerDropdown = document.getElementById('customer-dropdown');
        const customerId = document.getElementById('customer-id');

        // Handle customer search input
        customerSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();

            // Filter customers based on search term
            const filteredCustomers = allCustomers.filter(customer => {
                return customer.name.toLowerCase().includes(searchTerm) ||
                    (customer.phone && customer.phone.toLowerCase().includes(searchTerm)) ||
                    (customer.email && customer.email.toLowerCase().includes(searchTerm));
            });

            // Display customer suggestions
            displayCustomerSuggestions(filteredCustomers);
        });

        // Show all customers when search box is focused
        customerSearch.addEventListener('focus', function() {
            // Always show all customers when the search box gets focus
            // This ensures that newly added customers appear without reload
            displayCustomerSuggestions(allCustomers);
        });

        // Hide dropdown when clicking elsewhere
        document.addEventListener('click', function(e) {
            if (!customerSearch.contains(e.target) && !customerDropdown.contains(e.target)) {
                customerDropdown.style.display = 'none';
            }
        });

        // Function to display customer suggestions
        function displayCustomerSuggestions(customers) {
            customerDropdown.innerHTML = '';

            if (customers.length === 0) {
                const noResults = document.createElement('a');
                noResults.className = 'dropdown-item text-muted';
                noResults.href = '#';
                noResults.textContent = 'Tidak ada pelanggan ditemukan';
                noResults.onclick = function(e) {
                    e.preventDefault();
                };
                customerDropdown.appendChild(noResults);
                customerDropdown.style.display = 'block';
                return;
            }

            customers.forEach(customer => {
                const item = document.createElement('a');
                item.className = 'dropdown-item';
                item.href = '#';
                item.innerHTML = `
                <div class="d-flex justify-content-between">
                    <div>${customer.name}</div>
                    <div class="text-muted small">${customer.phone || customer.email || ''}</div>
                </div>
            `;

                item.onclick = function(e) {
                    e.preventDefault();
                    selectCustomer(customer);
                };

                customerDropdown.appendChild(item);
            });

            customerDropdown.style.display = 'block';
        }

        // Function to select a customer
        function selectCustomer(customer) {
            customerSearch.value = customer.name; // Display customer name in search box
            customerId.value = customer.id; // Store customer ID in hidden input
            customerDropdown.style.display = 'none'; // Hide the dropdown
            
            // Show confirmation of selected customer
            showNotification('Pelanggan "' + customer.name + '" telah dipilih', 'info');
        }

        // Add customer functionality (using event delegation)
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.id === 'add-customer-form') {
                e.preventDefault();

                const formData = {
                    name: document.getElementById('customer-name').value,
                    phone: document.getElementById('customer-phone').value,
                    email: document.getElementById('customer-email').value
                };

                // Clear previous errors
                document.getElementById('name-error').textContent = '';
                document.getElementById('phone-error').textContent = '';
                document.getElementById('email-error').textContent = '';

                document.querySelectorAll('#add-customer-form .form-control').forEach(input => {
                    input.classList.remove('is-invalid');
                });

                // Send customer data to API
                fetch('/api/customers', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + apiToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (response.ok) {
                            // The API might return the customer data differently, let's try multiple possible formats
                            let newCustomer;
                            if (data.customer) {
                                // If API returns customer object under 'customer' key
                                newCustomer = {
                                    id: data.customer.id,
                                    name: data.customer.name,
                                    phone: data.customer.phone,
                                    email: data.customer.email
                                };
                            } else if (data.data) {
                                // If API returns customer object under 'data' key
                                newCustomer = {
                                    id: data.data.id,
                                    name: data.data.name,
                                    phone: data.data.phone,
                                    email: data.data.email
                                };
                            } else if (data.id) {
                                // If the response itself is the customer object
                                newCustomer = {
                                    id: data.id,
                                    name: data.name || data.customer_name,
                                    phone: data.phone,
                                    email: data.email
                                };
                            } else {
                                // Fallback if none of the usual formats match
                                showNotification('Pelanggan berhasil ditambahkan!', 'success');
                                // Close the modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById(
                                    'addCustomerModal'));
                                if (modal) {
                                    modal.hide();
                                }
                                // Reset the form
                                document.getElementById('add-customer-form').reset();
                                return;
                            }
                            
                            // Add the new customer to our local array
                            allCustomers.push(newCustomer);

                            // Select the new customer
                            selectCustomer(newCustomer);

                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'addCustomerModal'));
                            if (modal) {
                                modal.hide();
                            }

                            // Reset the form
                            document.getElementById('add-customer-form').reset();
                            
                            // Show success notification
                            showNotification('Pelanggan "' + newCustomer.name + '" berhasil ditambahkan!', 'success');
                        } else {
                            // Handle validation errors
                            if (data.errors) {
                                if (data.errors.name) {
                                    document.getElementById('name-error').textContent = data.errors.name[0];
                                    document.getElementById('customer-name').classList.add('is-invalid');
                                }

                                if (data.errors.phone) {
                                    document.getElementById('phone-error').textContent = data.errors.phone[0];
                                    document.getElementById('customer-phone').classList.add('is-invalid');
                                }

                                if (data.errors.email) {
                                    document.getElementById('email-error').textContent = data.errors.email[0];
                                    document.getElementById('customer-email').classList.add('is-invalid');
                                }
                            } else {
                                showNotification('Kesalahan: ' + (data.message || 'Gagal menambahkan pelanggan'), 'danger');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Kesalahan:', error);
                        showNotification('Terjadi kesalahan saat menambahkan pelanggan: ' + error.message, 'danger');
                    });
            }
        });

        // Event listener for the add customer button (using event delegation)
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'add-customer-btn') {
                // Reset the form when the modal is opened
                setTimeout(() => {
                    const form = document.getElementById('add-customer-form');
                    if (form) {
                        form.reset();

                        // Clear any previous errors
                        document.getElementById('name-error').textContent = '';
                        document.getElementById('phone-error').textContent = '';
                        document.getElementById('email-error').textContent = '';

                        document.querySelectorAll('#add-customer-form .form-control').forEach(input => {
                            input.classList.remove('is-invalid');
                        });
                    }
                }, 300); // Delay to ensure modal is fully loaded
            }
        });

        // Product search and dropdown functionality
        const productDropdown = document.getElementById('product-dropdown');

        // Product search functionality with dropdown suggestions
        searchProductsInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();

            if (searchTerm.length === 0) {
                productDropdown.style.display = 'none';
                displayProducts(allProducts);
                return;
            }

            // Filter products based on search term
            const filteredProducts = allProducts.filter(product =>
                product.name.toLowerCase().includes(searchTerm) ||
                product.code.toLowerCase().includes(searchTerm)
            );

            if (filteredProducts.length > 0) {
                // Show dropdown with suggestions
                showProductSuggestions(filteredProducts);
                productDropdown.style.display = 'block';
            } else {
                // Show no results message
                productDropdown.innerHTML =
                    '<div class="dropdown-item text-muted">Tidak ada produk ditemukan</div>';
                productDropdown.style.display = 'block';
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchProductsInput.contains(e.target) && !productDropdown.contains(e.target)) {
                productDropdown.style.display = 'none';
            }
        });

        // Function to show product suggestions in dropdown
        function showProductSuggestions(products) {
            productDropdown.innerHTML = '';

            products.slice(0, 10).forEach(product => { // Limit to 10 suggestions
                const item = document.createElement('a');
                item.className = 'dropdown-item';
                item.href = '#';
                item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">${product.name}</div>
                        <div class="small text-muted">${product.code}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">Rp ${formatRupiah(product.price)}</div>
                        <div class="small text-success">Stock: ${product.stock}</div>
                    </div>
                </div>
            `;

                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    addToCart(product.id);
                    searchProductsInput.value = ''; // Clear search after selection
                    productDropdown.style.display = 'none';
                });

                productDropdown.appendChild(item);
            });
        }
    </script>

    <style>
        .max-height-300 {
            max-height: 300px;
            overflow-y: auto;
        }

        /* Custom styles for customer dropdown */
        #customer-search {
            position: relative;
            z-index: 1000;
        }

        #customer-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }

        .customer-search-container {
            position: relative;
        }

        /* Custom styles for product dropdown */
        #search-products {
            position: relative;
            z-index: 999;
        }

        #product-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
        }

        .product-search-container {
            position: relative;
        }
    </style>
@endsection
