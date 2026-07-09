<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel Product Sorting</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Sortable -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .table th {
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
        }

        .handle {
            cursor: grab;
            color: #0d6efd;
        }

        .handle:active {
            cursor: grabbing;
        }

        .sortable-ghost {
            opacity: .4;
            background: #dbeafe;
        }

        .pagination {
            justify-content: center;
        }

        footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">

        <div class="container">

            <a class="navbar-brand" href="{{ route('products.index') }}">
                <i class="fas fa-box-open"></i>
                Product Sorting System
            </a>

            <a href="{{ route('products.create') }}" class="btn btn-success">

                <i class="fas fa-plus-circle"></i>
                Add Product

            </a>

        </div>

    </nav>

    <div class="container mt-4">

        @yield('content')

    </div>

    <footer>

        Laravel 12 Drag & Drop Product Sorting |
        Developed with ❤️

    </footer>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        $.ajaxSetup({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }

        });

        // Success Message

        @if(session('success'))

            Swal.fire({

                toast: true,

                position: 'top-end',

                icon: 'success',

                title: '{{ session("success") }}',

                showConfirmButton: false,

                timer: 2000

            });

        @endif

    </script>

    @stack('scripts')

</body>

</html>