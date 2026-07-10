@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Dashboard Cards -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <h6>Total Products</h6>
                    <h2 class="text-primary">{{ $totalProducts }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6>Active Products</h6>
                    <h2 class="text-success">{{ $activeProducts }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <h6>Inactive Products</h6>
                    <h2 class="text-danger">{{ $inactiveProducts }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h6>Today's Products</h6>
                    <h2 class="text-info">{{ $todayProducts }}</h2>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Card -->

    <div class="card shadow">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                <i class="fas fa-boxes"></i>
                Product List
            </h4>

            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Product
            </a>

        </div>

        <div class="card-body">

            <!-- Search -->

            <form action="{{ route('products.index') }}" method="GET">

                <div class="row mb-4">

                    <div class="col-md-10">

                        <input type="text" name="search" class="form-control"
                            placeholder="Search by Name, Description or Price..." value="{{ request('search') }}">

                    </div>

                    <div class="col-md-2 d-grid">

                        <button class="btn btn-dark">
                            <i class="fas fa-search"></i>
                            Search
                        </button>

                    </div>

                </div>

            </form>

            <form method="POST"
                action="{{ route('products.bulk-action') }}"
                id="bulkForm">

                @csrf


                <div class="row mb-3">

                    <div class="col-md-6 d-flex gap-2">

                        <select name="action"
                            class="form-select"
                            required>

                            <option value="">
                                Select Action
                            </option>

                            <option value="activate">
                                Activate Selected
                            </option>

                            <option value="deactivate">
                                Deactivate Selected
                            </option>

                            <option value="delete">
                                Delete Selected
                            </option>

                        </select>


                        <button class="btn btn-dark">

                            <i class="fas fa-tasks"></i>
                            Apply

                        </button>


                        <button type="button"
                            id="resetOrder"
                            class="btn btn-warning">

                            <i class="fas fa-sort"></i>
                            Reset Order

                        </button>


                    </div>

                </div>

                @if($products->count())

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th width="50">

                                    <input type="checkbox" id="selectAll">

                                </th>

                                <th width="70">Order</th>

                                <th width="70">Drag</th>

                                <th>Name</th>

                                <th>Description</th>

                                <th width="120">Price</th>

                                <th width="120">Status</th>

                                <th width="140">Action</th>

                            </tr>

                        </thead>

                        <tbody id="sortable-list">

                            @foreach($products as $product)

                            <tr data-id="{{ $product->id }}">

                                <td>

                                    <input
                                        type="checkbox"
                                        name="product_ids[]"
                                        value="{{ $product->id }}"
                                        class="product-checkbox">

                                </td>

                                <td>

                                    <span class="badge bg-secondary order-badge">
                                        {{ $product->sort_order }}
                                    </span>

                                </td>

                                <td class="handle text-center">

                                    <i class="fas fa-bars fa-lg text-primary"></i>

                                </td>

                                <td>

                                    {{ $product->name }}

                                </td>

                                <td>

                                    {{ Str::limit($product->description, 60) }}

                                </td>

                                <td>

                                    ${{ number_format($product->price, 2) }}

                                </td>

                                <td>

                                    @if($product->is_active)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                    @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                    @endif

                                </td>

                                <td>

                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">

                                        <i class="fas fa-edit"></i>

                                    </a>

                                    <button type="button"
                                        class="btn btn-danger btn-sm delete-btn"
                                        data-id="{{ $product->id }}">

                                        <i class="fas fa-trash"></i>

                                    </button>
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </form>

            <!-- Pagination -->

            <div class="mt-3 d-flex justify-content-center">

                @if ($products->lastPage() > 1)
                <nav>
                    <ul class="pagination justify-content-center">

                        @for ($i = 1; $i <= $products->lastPage(); $i++)
                            <li class="page-item {{ $products->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $products->url($i) }}">
                                    {{ $i }}
                                </a>
                            </li>
                            @endfor

                    </ul>
                </nav>
                @endif
            </div>

            @else

            <div class="alert alert-warning">

                No Products Found.

            </div>

            @endif

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // -----------------------------
        // Drag & Drop Sorting
        // -----------------------------
        const sortableList = document.getElementById('sortable-list');

        if (sortableList) {

            new Sortable(sortableList, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'sortable-ghost',

                onEnd: function() {
                    updateOrder();
                }
            });

        }

        function updateOrder() {

            let items = [];

            document.querySelectorAll('#sortable-list tr').forEach(function(row, index) {

                items.push(row.dataset.id);

                row.querySelector('.order-badge').innerHTML = index + 1;

            });

            $.ajax({

                url: "{{ route('products.update-order') }}",

                method: "POST",

                data: {

                    _token: "{{ csrf_token() }}",

                    items: items

                },

                success: function() {

                    Swal.fire({

                        toast: true,

                        position: 'top-end',

                        icon: 'success',

                        title: 'Order Updated Successfully',

                        showConfirmButton: false,

                        timer: 1500

                    });

                },

                error: function() {

                    Swal.fire(

                        'Error',

                        'Unable to update order.',

                        'error'

                    );

                }

            });

        }

        // -----------------------------
        // SweetAlert Delete Confirmation
        // -----------------------------
        $('.delete-form').submit(function(e) {

            e.preventDefault();

            let form = this;

            Swal.fire({

                title: 'Delete Product?',

                text: "This action cannot be undone.",

                icon: 'warning',

                showCancelButton: true,

                confirmButtonColor: '#d33',

                cancelButtonColor: '#3085d6',

                confirmButtonText: 'Yes, Delete',

                cancelButtonText: 'Cancel'

            }).then((result) => {

                if (result.isConfirmed) {

                    form.submit();

                }

            });

        });

    });

    // Select All Checkbox

    $("#selectAll").click(function() {

        $(".product-checkbox")
            .prop(
                'checked',
                this.checked
            );

    });



    // Reset Sorting

    $("#resetOrder").click(function() {


        Swal.fire({

                title: "Reset Sorting?",

                text: "Products will be arranged by default order.",

                icon: "warning",

                showCancelButton: true


            })
            .then((result) => {


                if (result.isConfirmed) {


                    $.ajax({

                        url: "{{ route('products.reset-order') }}",

                        method: "POST",


                        success: function() {


                            Swal.fire({

                                toast: true,

                                position: "top-end",

                                icon: "success",

                                title: "Order Reset Successfully",

                                showConfirmButton: false,

                                timer: 1500


                            });


                            setTimeout(() => {

                                location.reload();

                            }, 1500);


                        }


                    });


                }


            });

    });


    // Bulk delete confirmation

    $("#bulkForm").submit(function(e) {


        let checked =
            $(".product-checkbox:checked").length;


        if (checked === 0) {

            e.preventDefault();


            Swal.fire(
                "Select Products",
                "Please select at least one product.",
                "warning"
            );


        }

    });

    $(".delete-btn").click(function() {

        let id = $(this).data('id');


        Swal.fire({

            title: "Delete Product?",

            text: "This action cannot be undone.",

            icon: "warning",

            showCancelButton: true

        }).then((result) => {


            if (result.isConfirmed) {


                let form = document.createElement('form');

                form.method = "POST";

                form.action = "/products/" + id;


                form.innerHTML = `

<input type="hidden" name="_token"
value="{{ csrf_token() }}">

<input type="hidden" name="_method"
value="DELETE">

`;


                document.body.appendChild(form);

                form.submit();


            }


        });


    });
</script>

@endpush