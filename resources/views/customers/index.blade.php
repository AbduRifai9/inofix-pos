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
                        <h2 class="h5 mb-0">Customers Management</h2>
                        <button id="add-customer-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
                            Tambah Pelanggan
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="customers-table" class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customers-list">
                                    <!-- Data will be loaded via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Tambah Pelanggan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customerForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="customer-id" name="id">
                    <div class="mb-3">
                        <label for="customer-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="customer-name" name="name" required>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="customer-phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="customer-phone" name="phone" required>
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="customer-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customer-email" name="email">
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-customer">Simpan Pelanggan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCustomerModalLabel">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <span id="view-customer-id"></span></p>
                        <p><strong>Name:</strong> <span id="view-customer-name"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Phone:</strong> <span id="view-customer-phone"></span></p>
                        <p><strong>Email:</strong> <span id="view-customer-email"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Created At:</strong> <span id="view-customer-created-at"></span></p>
                        <p><strong>Updated At:</strong> <span id="view-customer-updated-at"></span></p>
                    </div>
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
        table = $('#customers-table').DataTable({
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
                url: '/api/customers',
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
                    data: 'name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'phone',
                    defaultContent: 'N/A'
                },
                {
                    data: 'email',
                    defaultContent: 'N/A'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-outline-info view-customer"
                                    data-customer="${JSON.stringify(row).replace(/"/g, '&quot;')}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewCustomerModal">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary edit-customer"
                                    data-customer="${JSON.stringify(row).replace(/"/g, '&quot;')}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#customerModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-customer"
                                    data-id="${row.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ]
        });

        // Add customer button event
        document.getElementById('add-customer-btn').addEventListener('click', function() {
            // Reset form
            document.getElementById('customerForm').reset();
            document.getElementById('customerModalLabel').textContent = 'Tambah Pelanggan Baru';
            document.getElementById('save-customer').textContent = 'Simpan Pelanggan';
            document.getElementById('customer-id').value = '';

            // Clear errors
            clearCustomerErrors();
        });

        // Edit customer event (delegated event)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-customer')) {
                const button = e.target.closest('.edit-customer');
                const customer = JSON.parse(button.getAttribute('data-customer'));

                document.getElementById('customerModalLabel').textContent = 'Edit Pelanggan';
                document.getElementById('save-customer').textContent = 'Perbarui Pelanggan';

                document.getElementById('customer-id').value = customer.id;
                document.getElementById('customer-name').value = customer.name;
                document.getElementById('customer-phone').value = customer.phone;
                document.getElementById('customer-email').value = customer.email || '';

                // Clear errors
                clearCustomerErrors();
            }
        });

        // View customer event (delegated event)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-customer')) {
                const button = e.target.closest('.view-customer');
                const customer = JSON.parse(button.getAttribute('data-customer'));

                document.getElementById('view-customer-id').textContent = customer.id;
                document.getElementById('view-customer-name').textContent = customer.name;
                document.getElementById('view-customer-phone').textContent = customer.phone;
                document.getElementById('view-customer-email').textContent = customer.email || 'N/A';
                document.getElementById('view-customer-created-at').textContent = new Date(customer.created_at).toLocaleString();
                document.getElementById('view-customer-updated-at').textContent = new Date(customer.updated_at).toLocaleString();
            }
        });

        // Delete customer event (delegated event)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-customer')) {
                const button = e.target.closest('.delete-customer');
                const customerId = button.getAttribute('data-id');

                if (confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) {
                    deleteCustomer(customerId);
                }
            }
        });
    });

    // Form submission
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        clearCustomerErrors();

        const customerId = document.getElementById('customer-id');
        const customerName = document.getElementById('customer-name');
        const customerPhone = document.getElementById('customer-phone');
        const customerEmail = document.getElementById('customer-email');

        const formData = {
            name: customerName.value,
            phone: customerPhone.value,
            email: customerEmail.value || null
        };

        const saveCustomerBtn = document.getElementById('save-customer');

        if (customerId.value) {
            // Update existing customer
            updateCustomer(customerId.value, formData, saveCustomerBtn);
        } else {
            // Create new customer
            createCustomer(formData, saveCustomerBtn);
        }
    });

    // Create customer function
    function createCustomer(data, saveCustomerBtn) {
        saveCustomerBtn.disabled = true;
        saveCustomerBtn.textContent = 'Menyimpan...';

        fetch('/api/customers', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.message && result.data) {
                alert('Pelanggan berhasil dibuat!');

                // Close the modal
                const modalElement = document.getElementById('customerModal');
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.hide();

                // Reload the table data
                table.ajax.reload();
            } else {
                handleCustomerErrors(result.errors || result.message);
            }
        })
        .catch(error => {
            console.error('Kesalahan:', error);
            alert('Terjadi kesalahan saat membuat pelanggan: ' + error.message);
        })
        .finally(() => {
            saveCustomerBtn.disabled = false;
            saveCustomerBtn.textContent = 'Simpan Pelanggan';
        });
    }

    // Update customer function
    function updateCustomer(id, data, saveCustomerBtn) {
        saveCustomerBtn.disabled = true;
        saveCustomerBtn.textContent = 'Memperbarui...';

        fetch(`/api/customers/${id}`, {
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.message && result.data) {
                alert('Pelanggan berhasil diperbarui!');

                // Close the modal
                const modalElement = document.getElementById('customerModal');
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.hide();

                // Reload the table data
                table.ajax.reload();
            } else {
                handleCustomerErrors(result.errors || result.message);
            }
        })
        .catch(error => {
            console.error('Kesalahan:', error);
            alert('Terjadi kesalahan saat memperbarui pelanggan: ' + error.message);
        })
        .finally(() => {
            saveCustomerBtn.disabled = false;
            saveCustomerBtn.textContent = 'Perbarui Pelanggan';
        });
    }

    // Delete customer function
    function deleteCustomer(id) {
        fetch(`/api/customers/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.message) {
                alert('Pelanggan berhasil dihapus!');

                // Reload the table data
                table.ajax.reload();
            } else {
                alert('Terjadi kesalahan saat menghapus pelanggan: ' + (result.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Kesalahan:', error);
            alert('Terjadi kesalahan saat menghapus pelanggan: ' + error.message);
        });
    }

    // Function to handle form errors
    function handleCustomerErrors(errors) {
        // Clear previous errors
        clearCustomerErrors();

        if (typeof errors === 'string') {
            alert('Error: ' + errors);
            return;
        }

        // Display field-specific errors
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(field + '-error');
            if (errorElement) {
                errorElement.textContent = errors[field][0];
                errorElement.parentElement.querySelector('input, select, textarea').classList.add('is-invalid');
            }
        });
    }

    // Function to clear form errors
    function clearCustomerErrors() {
        const errorElements = document.querySelectorAll('.invalid-feedback');
        errorElements.forEach(element => {
            element.textContent = '';
        });

        const invalidInputs = document.querySelectorAll('.is-invalid');
        invalidInputs.forEach(input => {
            input.classList.remove('is-invalid');
        });
    }
</script>
@endsection
