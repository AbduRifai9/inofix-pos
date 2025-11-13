@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 fw-bold text-gray-800 mb-4">Dashboard</h2>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="p-3 rounded-circle bg-primary-light">
                                            <i class="fas fa-box fa-lg"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="card-title h5">Products</h3>
                                            <p class="card-text h4 fw-bold mb-0" id="product-count">0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="p-3 rounded-circle bg-success-light">
                                            <i class="fas fa-user-friends fa-lg"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="card-title h5">Customers</h3>
                                            <p class="card-text h4 fw-bold mb-0" id="customer-count">0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-purple text-white">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="p-3 rounded-circle bg-purple-light">
                                            <i class="fas fa-shopping-cart fa-lg"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="card-title h5">Transactions</h3>
                                            <p class="card-text h4 fw-bold mb-0" id="transaction-count">0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('products.index') }}" class="card h-100 text-decoration-none shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="p-3 rounded-circle bg-blue-100 text-blue-600 mx-auto mb-3">
                                            <i class="fas fa-box fa-xl"></i>
                                        </div>
                                        <h3 class="h6 card-title">Manage Products</h3>
                                        <p class="card-text text-gray-600">View, add, and edit products</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 mb-3">
                                <a href="{{ route('customers.index') }}" class="card h-100 text-decoration-none shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="p-3 rounded-circle bg-green-100 text-green-600 mx-auto mb-3">
                                            <i class="fas fa-user-friends fa-xl"></i>
                                        </div>
                                        <h3 class="h6 card-title">Manage Customers</h3>
                                        <p class="card-text text-gray-600">View, add, and edit customers</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 mb-3">
                                <a href="{{ route('transactions.index') }}" class="card h-100 text-decoration-none shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="p-3 rounded-circle bg-purple-100 text-purple-600 mx-auto mb-3">
                                            <i class="fas fa-shopping-cart fa-xl"></i>
                                        </div>
                                        <h3 class="h6 card-title">View Transactions</h3>
                                        <p class="card-text text-gray-600">View sales history and reports</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h3 class="h6 mb-0 text-gray-800">Recent Transactions</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">Invoice</th>
                                                <th scope="col">Customer</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Total</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recent-transactions">
                                            <!-- Transactions will be loaded here -->
                                            <tr>
                                                <td colspan="5" class="text-center text-gray-500 py-4">No transactions found</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data for dashboard - in real app, this would come from API
        const sampleProducts = [
            { id: 1, name: 'Laptop Gaming', code: 'LAP001', price: 12000000, stock: 10 },
            { id: 2, name: 'Smartphone', code: 'SPH001', price: 5000000, stock: 25 },
            { id: 3, name: 'Mouse Wireless', code: 'MSE001', price: 250000, stock: 50 }
        ];

        const sampleCustomers = [
            { id: 1, name: 'John Doe', phone: '081234567890', email: 'john@example.com' },
            { id: 2, name: 'Jane Smith', phone: '082345678901', email: 'jane@example.com' },
            { id: 3, name: 'Robert Johnson', phone: '083456789012', email: 'robert@example.com' }
        ];

        const sampleTransactions = [
            { id: 1, invoice: 'INV202511130001', customer: 'John Doe', date: '2025-11-13', total: 12500000, status: 'Completed' },
            { id: 2, invoice: 'INV202511130002', customer: 'Jane Smith', date: '2025-11-13', total: 5250000, status: 'Completed' },
            { id: 3, invoice: 'INV202511120001', customer: 'Robert Johnson', date: '2025-11-12', total: 250000, status: 'Completed' }
        ];

        // DOM Elements
        const productCount = document.getElementById('product-count');
        const customerCount = document.getElementById('customer-count');
        const transactionCount = document.getElementById('transaction-count');
        const recentTransactions = document.getElementById('recent-transactions');

        // Initialize dashboard data
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });

        function loadDashboardData() {
            // Update counts
            productCount.textContent = sampleProducts.length;
            customerCount.textContent = sampleCustomers.length;
            transactionCount.textContent = sampleTransactions.length;

            // Update recent transactions
            recentTransactions.innerHTML = '';
            sampleTransactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${transaction.invoice}</td>
                    <td>${transaction.customer}</td>
                    <td>${transaction.date}</td>
                    <td>Rp ${formatRupiah(transaction.total)}</td>
                    <td>
                        <span class="badge bg-success">${transaction.status}</span>
                    </td>
                `;
                recentTransactions.appendChild(row);
            });
        }

        function formatRupiah(angka) {
            const reverse = angka.toString().split('').reverse().join('');
            const ribuan = reverse.match(/\d{1,3}/g);
            const hasil = ribuan.join('.').split('').reverse().join('');
            return hasil;
        }
    </script>
</div>
@endsection