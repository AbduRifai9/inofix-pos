@extends('layouts.app')

@section('content')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

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
                        <div class="table-responsive">
                            <table id="transactions-table" class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Invoice</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Subtotal</th>
                                        <th scope="col">Discount</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Transaction Modal -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTransactionModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Invoice:</strong> <span id="view-transaction-invoice"></span></p>
                        <p><strong>Customer:</strong> <span id="view-transaction-customer"></span></p>
                        <p><strong>Date:</strong> <span id="view-transaction-date"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Subtotal:</strong> <span id="view-transaction-subtotal"></span></p>
                        <p><strong>Discount:</strong> <span id="view-transaction-discount"></span></p>
                        <p><strong>Total:</strong> <span id="view-transaction-total"></span></p>
                    </div>
                </div>

                <h6>Items:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="transaction-items">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Hidden input to store the API token -->
<input type="hidden" id="api-token" value="{{ $api_token }}">

<script>
    // Get the API token from the hidden input
    const apiToken = document.getElementById('api-token').value;

    // Initialize DataTable
    let table;
    $(document).ready(function() {
        table = $('#transactions-table').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            ordering: true,
            paging: true,
            pageLength: 10,
            lengthChange: true,
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' // Indonesian language
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
                    return json.data || json; // Handle both paginated and non-paginated responses
                }
            },
            columns: [
                {
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
                        if (type === 'display' || type === 'filter') {
                            if (data) {
                                const date = new Date(data);
                                return date.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }
                            return 'N/A';
                        }
                        return data; // Return raw data for sorting
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
                        return data || 0; // Return the raw value for sorting
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
                        return data || 0; // Return the raw value for sorting
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
                        return data || 0; // Return the raw value for sorting
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-outline-info view-transaction"
                                    data-transaction="${JSON.stringify(row).replace(/"/g, '&quot;')}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewTransactionModal">
                                <i class="fas fa-eye"></i>
                            </button>
                        `;
                    }
                }
            ]
        });

        // View transaction event (delegated event)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-transaction')) {
                const button = e.target.closest('.view-transaction');
                const transaction = JSON.parse(button.getAttribute('data-transaction'));

                document.getElementById('view-transaction-invoice').textContent = transaction.invoice;
                document.getElementById('view-transaction-customer').textContent = transaction.customer?.name || 'Walk-in Customer';
                document.getElementById('view-transaction-date').textContent = new Date(transaction.created_at).toLocaleString();
                document.getElementById('view-transaction-subtotal').textContent = 'Rp ' + (transaction.subtotal || 0).toLocaleString('id-ID');
                document.getElementById('view-transaction-discount').textContent = 'Rp ' + (transaction.discount || 0).toLocaleString('id-ID');
                document.getElementById('view-transaction-total').textContent = 'Rp ' + (transaction.total || 0).toLocaleString('id-ID');

                // Populate transaction items
                const itemsContainer = document.getElementById('transaction-items');
                itemsContainer.innerHTML = '';

                if (transaction.items && transaction.items.length > 0) {
                    transaction.items.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.product?.name || item.product_name || 'Unknown Product'}</td>
                            <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                            <td>${item.quantity}</td>
                            <td>Rp ${(item.subtotal || item.price * item.quantity).toLocaleString('id-ID')}</td>
                        `;
                        itemsContainer.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="4" class="text-center">No items found</td>';
                    itemsContainer.appendChild(row);
                }
            }
        });
    });
</script>
@endsection
