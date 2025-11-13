@extends('layouts.app')

@section('content')
<div class="py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Customers Management</h2>
                        <button id="add-customer-btn" class="btn btn-primary">
                            Add Customer
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <!-- Search -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <input type="text" id="search-customers" placeholder="Search customers..." class="form-control">
                            </div>
                        </div>

                        <!-- Customers Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customers-list">
                                    <!-- Customers will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Customers pagination">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination links will be loaded here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customer-form">
                        <input type="hidden" id="customer-id">
                        <div class="mb-3">
                            <label for="customer-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="customer-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="customer-phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customer-email">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="customer-form" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample customers data - in real app, this would come from API
        let customers = [
            { id: 1, name: 'John Doe', phone: '081234567890', email: 'john@example.com' },
            { id: 2, name: 'Jane Smith', phone: '082345678901', email: 'jane@example.com' },
            { id: 3, name: 'Robert Johnson', phone: '083456789012', email: 'robert@example.com' },
            { id: 4, name: 'Emily Davis', phone: '084567890123', email: 'emily@example.com' },
            { id: 5, name: 'Michael Wilson', phone: '085678901234', email: 'michael@example.com' }
        ];

        // DOM Elements
        const customersList = document.getElementById('customers-list');
        const addCustomerBtn = document.getElementById('add-customer-btn');
        const customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
        const customerForm = document.getElementById('customer-form');
        const customerId = document.getElementById('customer-id');
        const customerName = document.getElementById('customer-name');
        const customerPhone = document.getElementById('customer-phone');
        const customerEmail = document.getElementById('customer-email');
        const searchInput = document.getElementById('search-customers');
        const modalTitle = document.getElementById('modal-title');

        // Initialize the customers page
        document.addEventListener('DOMContentLoaded', function() {
            loadCustomers();

            // Event listeners
            addCustomerBtn.addEventListener('click', openAddCustomerModal);
            customerForm.addEventListener('submit', saveCustomer);
            searchInput.addEventListener('input', filterCustomers);
        });

        // Load customers function
        function loadCustomers() {
            customersList.innerHTML = '';

            customers.forEach(customer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${customer.name}</td>
                    <td>${customer.phone}</td>
                    <td>${customer.email}</td>
                    <td>
                        <button onclick="editCustomer(${customer.id})" class="btn btn-sm btn-outline-primary me-1">Edit</button>
                        <button onclick="deleteCustomer(${customer.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                    </td>
                `;
                customersList.appendChild(row);
            });
        }

        // Open add customer modal
        function openAddCustomerModal() {
            modalTitle.textContent = 'Add New Customer';
            customerId.value = '';
            customerName.value = '';
            customerPhone.value = '';
            customerEmail.value = '';
            customerModal.show();
        }

        // Edit customer
        function editCustomer(id) {
            const customer = customers.find(c => c.id === id);
            if (customer) {
                modalTitle.textContent = 'Edit Customer';
                customerId.value = customer.id;
                customerName.value = customer.name;
                customerPhone.value = customer.phone;
                customerEmail.value = customer.email;
                customerModal.show();
            }
        }

        // Delete customer
        function deleteCustomer(id) {
            if (confirm('Are you sure you want to delete this customer?')) {
                customers = customers.filter(c => c.id !== id);
                loadCustomers();
            }
        }

        // Save customer (add or update)
        function saveCustomer(e) {
            e.preventDefault();

            const id = customerId.value ? parseInt(customerId.value) : null;
            const name = customerName.value;
            const phone = customerPhone.value;
            const email = customerEmail.value;

            if (id) {
                // Update existing customer
                const index = customers.findIndex(c => c.id === id);
                if (index !== -1) {
                    customers[index] = { id, name, phone, email };
                }
            } else {
                // Add new customer
                const newId = customers.length > 0 ? Math.max(...customers.map(c => c.id)) + 1 : 1;
                customers.push({ id: newId, name, phone, email });
            }

            loadCustomers();
            customerModal.hide();
        }

        // Filter customers based on search
        function filterCustomers() {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredCustomers = customers.filter(customer =>
                customer.name.toLowerCase().includes(searchTerm) ||
                customer.phone.toLowerCase().includes(searchTerm) ||
                customer.email.toLowerCase().includes(searchTerm)
            );

            customersList.innerHTML = '';

            if (filteredCustomers.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="4" class="text-center">No customers found</td>`;
                customersList.appendChild(row);
            } else {
                filteredCustomers.forEach(customer => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${customer.name}</td>
                        <td>${customer.phone}</td>
                        <td>${customer.email}</td>
                        <td>
                            <button onclick="editCustomer(${customer.id})" class="btn btn-sm btn-outline-primary me-1">Edit</button>
                            <button onclick="deleteCustomer(${customer.id})" class="btn btn-sm btn-outline-danger">Delete</button>
                        </td>
                    `;
                    customersList.appendChild(row);
                });
            }
        }
    </script>
</div>
@endsection
