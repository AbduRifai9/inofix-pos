@extends('layouts.app')

@section('content')
<div class="py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Products Management</h2>
                        <button id="add-product-btn" class="btn btn-primary">
                            Add Product
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <input type="text" id="search-products" placeholder="Search products..." class="form-control">
                            </div>
                            <div class="col-md-3">
                                <select id="filter-category" class="form-select">
                                    <option value="">All Categories</option>
                                    <option value="electronics">Electronics</option>
                                    <option value="accessories">Accessories</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Stock</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="products-list">
                                    <!-- Products will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Products pagination">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination links will be loaded here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="product-form">
                        <input type="hidden" id="product-id">
                        <div class="mb-3">
                            <label for="product-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="product-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="product-code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="product-code" required>
                        </div>
                        <div class="mb-3">
                            <label for="product-price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="product-price" required>
                        </div>
                        <div class="mb-3">
                            <label for="product-stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="product-stock" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="product-form" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample products data - in real app, this would come from API
        let products = [
            { id: 1, name: 'Laptop Gaming', code: 'LAP001', price: 12000000, stock: 10 },
            { id: 2, name: 'Smartphone', code: 'SPH001', price: 5000000, stock: 25 },
            { id: 3, name: 'Mouse Wireless', code: 'MSE001', price: 250000, stock: 50 },
            { id: 4, name: 'Keyboard Mechanical', code: 'KBD001', price: 800000, stock: 30 },
            { id: 5, name: 'Monitor 24 inch', code: 'MON001', price: 2000000, stock: 15 },
            { id: 6, name: 'Headphones', code: 'HPH001', price: 600000, stock: 40 },
            { id: 7, name: 'USB Cable', code: 'USB001', price: 75000, stock: 100 },
            { id: 8, name: 'External Hard Drive', code: 'HDD001', price: 1200000, stock: 20 }
        ];

        // DOM Elements
        const productsList = document.getElementById('products-list');
        const addProductBtn = document.getElementById('add-product-btn');
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        const productForm = document.getElementById('product-form');
        const productId = document.getElementById('product-id');
        const productName = document.getElementById('product-name');
        const productCode = document.getElementById('product-code');
        const productPrice = document.getElementById('product-price');
        const productStock = document.getElementById('product-stock');
        const searchInput = document.getElementById('search-products');
        const modalTitle = document.getElementById('modal-title');

        // Initialize the products page
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            
            // Event listeners
            addProductBtn.addEventListener('click', openAddProductModal);
            productForm.addEventListener('submit', saveProduct);
            searchInput.addEventListener('input', filterProducts);
        });

        // Load products function
        function loadProducts() {
            productsList.innerHTML = '';
            
            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.name}</td>
                    <td>${product.code}</td>
                    <td>Rp ${formatRupiah(product.price)}</td>
                    <td>${product.stock}</td>
                    <td>
                        <span class="badge ${product.stock > 0 ? 'bg-success' : 'bg-danger'}">
                            ${product.stock > 0 ? 'In Stock' : 'Out of Stock'}
                        </span>
                    </td>
                    <td>
                        <button onclick="editProduct(${product.id})" class="btn btn-sm btn-outline-primary me-1">Edit</button>
                        <button onclick="deleteProduct(${product.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                    </td>
                `;
                productsList.appendChild(row);
            });
        }

        // Open add product modal
        function openAddProductModal() {
            modalTitle.textContent = 'Add New Product';
            productId.value = '';
            productName.value = '';
            productCode.value = '';
            productPrice.value = '';
            productStock.value = '';
            productModal.show();
        }

        // Edit product
        function editProduct(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                modalTitle.textContent = 'Edit Product';
                productId.value = product.id;
                productName.value = product.name;
                productCode.value = product.code;
                productPrice.value = product.price;
                productStock.value = product.stock;
                productModal.show();
            }
        }

        // Delete product
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                products = products.filter(p => p.id !== id);
                loadProducts();
            }
        }

        // Save product (add or update)
        function saveProduct(e) {
            e.preventDefault();
            
            const id = productId.value ? parseInt(productId.value) : null;
            const name = productName.value;
            const code = productCode.value;
            const price = parseFloat(productPrice.value);
            const stock = parseInt(productStock.value);
            
            if (id) {
                // Update existing product
                const index = products.findIndex(p => p.id === id);
                if (index !== -1) {
                    products[index] = { id, name, code, price, stock };
                }
            } else {
                // Add new product
                const newId = products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1;
                products.push({ id: newId, name, code, price, stock });
            }
            
            loadProducts();
            productModal.hide();
        }

        // Filter products based on search
        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredProducts = products.filter(product => 
                product.name.toLowerCase().includes(searchTerm) || 
                product.code.toLowerCase().includes(searchTerm)
            );
            
            productsList.innerHTML = '';
            
            if (filteredProducts.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="6" class="text-center">No products found</td>`;
                productsList.appendChild(row);
            } else {
                filteredProducts.forEach(product => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${product.name}</td>
                        <td>${product.code}</td>
                        <td>Rp ${formatRupiah(product.price)}</td>
                        <td>${product.stock}</td>
                        <td>
                            <span class="badge ${product.stock > 0 ? 'bg-success' : 'bg-danger'}">
                                ${product.stock > 0 ? 'In Stock' : 'Out of Stock'}
                            </span>
                        </td>
                        <td>
                            <button onclick="editProduct(${product.id})" class="btn btn-sm btn-outline-primary me-1">Edit</button>
                            <button onclick="deleteProduct(${product.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                        </td>
                    `;
                    productsList.appendChild(row);
                });
            }
        }

        // Format currency function
        function formatRupiah(angka) {
            const reverse = angka.toString().split('').reverse().join('');
            const ribuan = reverse.match(/\d{1,3}/g);
            const hasil = ribuan.join('.').split('').reverse().join('');
            return hasil;
        }
    </script>
</div>
@endsection