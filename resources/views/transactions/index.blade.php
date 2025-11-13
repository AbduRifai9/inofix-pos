@extends('layouts.app')

@section('content')
<div class="py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Transactions Management</h2>
                        <a href="/pos" class="btn btn-success">
                            New Transaction
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <input type="text" id="search-transactions" placeholder="Search transactions..." class="form-control">
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="date-from" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="date-to" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <select id="filter-status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>

                        <!-- Transactions Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Invoice</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Subtotal</th>
                                        <th scope="col">Discount</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-list">
                                    <!-- Transactions will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Transactions pagination">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination links will be loaded here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h5>Transaction Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Invoice:</strong> <span id="transaction-invoice"></span></p>
                                <p class="mb-1"><strong>Date:</strong> <span id="transaction-date"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Customer:</strong> <span id="transaction-customer"></span></p>
                                <p class="mb-1"><strong>Status:</strong> <span id="transaction-status"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Items</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="transaction-items">
                                    <!-- Items will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row border-top pt-3">
                        <div class="col-md-6">
                            <p><strong>Subtotal:</strong> <span id="transaction-subtotal"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-danger"><strong>Discount:</strong> <span id="transaction-discount"></span></p>
                        </div>
                    </div>
                    <div class="row border-top pt-2">
                        <div class="col-md-12">
                            <p class="h5"><strong>Total:</strong> <span id="transaction-total"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample transactions data - in real app, this would come from API
        const transactions = [
            { 
                id: 1, 
                invoice: 'INV202511130001', 
                customer: 'John Doe', 
                date: '2025-11-13', 
                subtotal: 13000000, 
                discount: 1500000,
                total: 11500000, 
                status: 'Completed',
                items: [
                    { product: 'Laptop Gaming', quantity: 1, price: 12000000, subtotal: 12000000 },
                    { product: 'Mouse Wireless', quantity: 2, price: 250000, subtotal: 500000 }
                ]
            },
            { 
                id: 2, 
                invoice: 'INV202511130002', 
                customer: 'Jane Smith', 
                date: '2025-11-13', 
                subtotal: 5500000, 
                discount: 550000,
                total: 4950000, 
                status: 'Completed',
                items: [
                    { product: 'Smartphone', quantity: 1, price: 5000000, subtotal: 5000000 },
                    { product: 'USB Cable', quantity: 2, price: 75000, subtotal: 150000 }
                ]
            },
            { 
                id: 3, 
                invoice: 'INV202511120001', 
                customer: 'Robert Johnson', 
                date: '2025-11-12', 
                subtotal: 250000, 
                discount: 0,
                total: 250000, 
                status: 'Completed',
                items: [
                    { product: 'Mouse Wireless', quantity: 1, price: 250000, subtotal: 250000 }
                ]
            },
            { 
                id: 4, 
                invoice: 'INV202511110001', 
                customer: 'Emily Davis', 
                date: '2025-11-11', 
                subtotal: 2800000, 
                discount: 280000,
                total: 2520000, 
                status: 'Completed',
                items: [
                    { product: 'Monitor 24 inch', quantity: 1, price: 2000000, subtotal: 2000000 },
                    { product: 'Keyboard Mechanical', quantity: 1, price: 800000, subtotal: 800000 }
                ]
            }
        ];

        // DOM Elements
        const transactionsList = document.getElementById('transactions-list');
        const transactionModal = new bootstrap.Modal(document.getElementById('transactionModal'));
        const transactionInvoice = document.getElementById('transaction-invoice');
        const transactionDate = document.getElementById('transaction-date');
        const transactionCustomer = document.getElementById('transaction-customer');
        const transactionStatus = document.getElementById('transaction-status');
        const transactionItems = document.getElementById('transaction-items');
        const transactionSubtotal = document.getElementById('transaction-subtotal');
        const transactionDiscount = document.getElementById('transaction-discount');
        const transactionTotal = document.getElementById('transaction-total');
        const searchInput = document.getElementById('search-transactions');

        // Initialize the transactions page
        document.addEventListener('DOMContentLoaded', function() {
            loadTransactions();
            
            // Event listeners
            searchInput.addEventListener('input', filterTransactions);
        });

        // Load transactions function
        function loadTransactions() {
            transactionsList.innerHTML = '';
            
            transactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${transaction.invoice}</td>
                    <td>${transaction.customer}</td>
                    <td>${transaction.date}</td>
                    <td>Rp ${formatRupiah(transaction.subtotal)}</td>
                    <td class="text-danger">Rp ${formatRupiah(transaction.discount)}</td>
                    <td class="fw-bold">Rp ${formatRupiah(transaction.total)}</td>
                    <td>
                        <span class="badge bg-success">${transaction.status}</span>
                    </td>
                    <td>
                        <button onclick="viewTransaction(${transaction.id})" class="btn btn-sm btn-outline-primary me-1">View</button>
                        <button onclick="deleteTransaction(${transaction.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                    </td>
                `;
                transactionsList.appendChild(row);
            });
        }

        // View transaction details
        function viewTransaction(id) {
            const transaction = transactions.find(t => t.id === id);
            if (transaction) {
                transactionInvoice.textContent = transaction.invoice;
                transactionDate.textContent = transaction.date;
                transactionCustomer.textContent = transaction.customer;
                transactionStatus.textContent = transaction.status;
                
                // Load items
                transactionItems.innerHTML = '';
                transaction.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.product}</td>
                        <td>${item.quantity}</td>
                        <td>Rp ${formatRupiah(item.price)}</td>
                        <td>Rp ${formatRupiah(item.subtotal)}</td>
                    `;
                    transactionItems.appendChild(row);
                });
                
                transactionSubtotal.textContent = `Rp ${formatRupiah(transaction.subtotal)}`;
                transactionDiscount.textContent = `Rp ${formatRupiah(transaction.discount)}`;
                transactionTotal.textContent = `Rp ${formatRupiah(transaction.total)}`;
                
                transactionModal.show();
            }
        }

        // Delete transaction
        function deleteTransaction(id) {
            if (confirm('Are you sure you want to delete this transaction?')) {
                // In a real app, you would call the API to delete
                console.log('Deleting transaction:', id);
                loadTransactions(); // Refresh the list
            }
        }

        // Filter transactions based on search
        function filterTransactions() {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredTransactions = transactions.filter(transaction => 
                transaction.invoice.toLowerCase().includes(searchTerm) || 
                transaction.customer.toLowerCase().includes(searchTerm) ||
                transaction.total.toString().includes(searchTerm)
            );
            
            transactionsList.innerHTML = '';
            
            if (filteredTransactions.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="8" class="text-center">No transactions found</td>`;
                transactionsList.appendChild(row);
            } else {
                filteredTransactions.forEach(transaction => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${transaction.invoice}</td>
                        <td>${transaction.customer}</td>
                        <td>${transaction.date}</td>
                        <td>Rp ${formatRupiah(transaction.subtotal)}</td>
                        <td class="text-danger">Rp ${formatRupiah(transaction.discount)}</td>
                        <td class="fw-bold">Rp ${formatRupiah(transaction.total)}</td>
                        <td>
                            <span class="badge bg-success">${transaction.status}</span>
                        </td>
                        <td>
                            <button onclick="viewTransaction(${transaction.id})" class="btn btn-sm btn-outline-primary me-1">View</button>
                            <button onclick="deleteTransaction(${transaction.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                        </td>
                    `;
                    transactionsList.appendChild(row);
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