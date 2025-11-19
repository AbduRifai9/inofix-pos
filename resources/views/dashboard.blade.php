@extends('layouts.app')

@section('content')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS for clickable cards -->
    <style>
        .clickable-card {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .clickable-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .clickable-card:active {
            transform: translateY(-1px);
        }
    </style>

    <div class="py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="h4 fw-bold mb-4">Dashboard</h2>

                            <!-- Hidden input to store the API token -->
                            <input type="hidden" id="api-token" value="{{ $api_token }}">

                            <!-- Stats Cards -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white clickable-card">
                                        <a href="{{ route('products.index') }}"
                                            class="stretched-link text-white text-decoration-none">
                                            <div class="card-body d-flex align-items-center">
                                                <div class="p-3 rounded-circle bg-primary bg-opacity-25">
                                                    <i class="fas fa-box fa-lg"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h3 class="card-title h5">Products</h3>
                                                    <p class="card-text h4 fw-bold mb-0" id="product-count">
                                                        {{ $product_count }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-success text-white clickable-card">
                                        <a href="{{ route('customers.index') }}"
                                            class="stretched-link text-white text-decoration-none">
                                            <div class="card-body d-flex align-items-center">
                                                <div class="p-3 rounded-circle bg-success bg-opacity-25">
                                                    <i class="fas fa-users fa-lg"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h3 class="card-title h5">Customers</h3>
                                                    <p class="card-text h4 fw-bold mb-0" id="customer-count">
                                                        {{ $customer_count }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-info text-white clickable-card">
                                        <a href="{{ route('transactions.index') }}"
                                            class="stretched-link text-white text-decoration-none">
                                            <div class="card-body d-flex align-items-center">
                                                <div class="p-3 rounded-circle bg-info bg-opacity-25">
                                                    <i class="fas fa-shopping-cart fa-lg"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h3 class="card-title h5">Transactions</h3>
                                                    <p class="card-text h4 fw-bold mb-0" id="transaction-count">
                                                        {{ $transaction_count }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="h5 mb-3">Recent Transactions</h3>
                                    <div class="table-responsive">
                                        <table id="recent-transactions-table" class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">Invoice</th>
                                                    <th scope="col">Customer</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Subtotal</th>
                                                    <th scope="col">Discount</th>
                                                    <th scope="col">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recent-transactions">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        Loading transactions...
                                                    </td>
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
    </div>

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const apiToken = $('#api-token').val();
            const table = $('#recent-transactions-table').DataTable({
                processing: true,
                serverSide: false,
                searching: true,
                ordering: true,
                paging: true,
                pageLength: 10,
                lengthChange: true,
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                ajax: {
                    url: '/api/transactions',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + apiToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    dataSrc: function(json) {
                        if (json.data && json.data.length > 0) {
                            return json.data.filter(function(item) {
                                return item.status === 'success' || item.status === 'paid' ||
                                    item.status === 'settlement';
                            });
                        }
                        return [];
                    }
                },
                columns: [{
                        data: 'invoice',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'customer',
                        render: function(data, type, row) {
                            return data && data.name ? data.name : 'Walk-in Customer';
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data, type, row) {
                            if (data) {
                                const date = new Date(data);
                                return date.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                            }
                            return 'N/A';
                        }
                    },
                    {
                        data: 'subtotal',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'discount',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'total',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                            return data || 0;
                        }
                    }
                ],
                initComplete: function() {
                    // DataTables initialized successfully
                    console.log('DataTable initialized with API data');
                },
                drawCallback: function(settings) {}
            });
            const initialTransactions = @json($recent_transactions ?? []);
            if (initialTransactions.length > 0) {
                table.clear();
                for (let i = 0; i < initialTransactions.length; i++) {
                    const transaction = initialTransactions[i];
                    table.row.add({
                        'invoice': transaction.invoice || 'N/A',
                        'customer': transaction.customer || {
                            'name': 'Walk-in Customer'
                        },
                        'created_at': transaction.created_at,
                        'subtotal': transaction.subtotal || 0,
                        'discount': transaction.discount || 0,
                        'total': transaction.total || 0
                    });
                }
                table.draw();
            } else {
                table.ajax.reload();
            }
        });
    </script>
@endsection
