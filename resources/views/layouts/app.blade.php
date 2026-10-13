<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Modern Product Catalog & Drag-Drop Sorting</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6.7 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --brand-primary: #4F46E5;
            --brand-primary-rgb: 79, 70, 229;
            --brand-hover: #4338CA;
            --card-radius: 16px;
            --btn-radius: 10px;
        }

        body {
            font-family: var(--font-main);
            background-color: #F8FAFC;
            color: #0F172A;
            transition: background-color 0.25s ease, color 0.25s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        [data-bs-theme="dark"] body {
            background-color: #0B0F19;
            color: #E2E8F0;
        }

        [data-bs-theme="dark"] .bg-white {
            background-color: #111827 !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #111827;
            border-color: #1F2937;
        }

        [data-bs-theme="dark"] .table {
            --bs-table-bg: #111827;
            --bs-table-color: #E2E8F0;
            --bs-table-border-color: #1F2937;
            --bs-table-hover-bg: #1A2234;
        }

        [data-bs-theme="dark"] .border,
        [data-bs-theme="dark"] .border-top,
        [data-bs-theme="dark"] .border-bottom,
        [data-bs-theme="dark"] .border-end,
        [data-bs-theme="dark"] .border-start {
            border-color: #1F2937 !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #0F172A;
            border-color: #334155;
            color: #F1F5F9;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #0F172A;
            border-color: var(--brand-primary);
            color: #FFF;
        }

        [data-bs-theme="dark"] .input-group-text {
            background-color: #1E293B;
            border-color: #334155;
            color: #94A3B8;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.02em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #4F46E5, #06B6D4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        /* Card Modern Styling */
        .card {
            border: 1px solid #E2E8F0;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        /* Stat Cards */
        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            padding: 18px 20px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        [data-bs-theme="dark"] .stat-card {
            background: #111827;
            border-color: #1F2937;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Modern Table & Visual Drag Handle */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            border-bottom: 1px solid #E2E8F0;
            padding: 12px 14px;
        }

        .table tbody td {
            padding: 12px 14px;
            vertical-align: middle;
            font-size: 13.5px;
        }

        /* Drag Handle Styling */
        .drag-handle {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94A3B8;
            cursor: grab;
            transition: all 0.15s ease;
            user-select: none;
        }

        .drag-handle:hover {
            background-color: rgba(79, 70, 229, 0.12);
            color: var(--brand-primary);
            transform: scale(1.08);
        }

        .drag-handle:active {
            cursor: grabbing;
            transform: scale(0.95);
        }

        .sortable-ghost {
            opacity: 0.35;
            background-color: #EEF2FF !important;
            border: 2px dashed #4F46E5 !important;
        }

        [data-bs-theme="dark"] .sortable-ghost {
            background-color: rgba(79, 70, 229, 0.15) !important;
            border-color: #818CF8 !important;
        }

        /* Inline Quick Edit Cells */
        .editable-cell {
            position: relative;
            cursor: pointer;
            border-radius: 6px;
            padding: 4px 6px;
            margin: -4px -6px;
            transition: background-color 0.15s ease;
        }

        .editable-cell:hover {
            background-color: rgba(79, 70, 229, 0.08);
            outline: 1px dashed rgba(79, 70, 229, 0.4);
        }

        .editable-cell .edit-hint {
            display: none;
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            color: #6366F1;
        }

        .editable-cell:hover .edit-hint {
            display: inline-block;
        }

        /* Badges & Font */
        .font-mono {
            font-family: var(--font-mono);
        }

        .product-thumbnail-img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
        }

        /* Switch Toggle */
        .theme-toggle-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.08);
            color: #F8FAFC;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #FFF;
        }

        footer {
            margin-top: auto;
            border-top: 1px solid #E2E8F0;
            padding: 24px 0;
            background: #FFFFFF;
        }

        [data-bs-theme="dark"] footer {
            background: #111827;
            border-color: #1F2937;
        }
    </style>
</head>

<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand" href="{{ route('products.index') }}">
                <span class="brand-logo-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </span>
                <span>Product Master <span class="badge bg-primary text-white ms-1" style="font-size:10px; vertical-align:middle;">L12 Drag-Drop</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
                    <!-- Catalog View Link -->
                    <li class="nav-item">
                        <a class="nav-link {{ !request('trash') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="fas fa-list-check me-1"></i> Active Catalog
                        </a>
                    </li>

                    <!-- Recycle Bin Link -->
                    <li class="nav-item">
                        <a class="nav-link {{ request('trash') ? 'active text-warning' : '' }}" href="{{ route('products.index', ['trash' => 1]) }}">
                            <i class="fas fa-trash-can me-1"></i> Recycle Bin
                            @php
                                $trashBadgeCount = \App\Models\Product::onlyTrashed()->count();
                            @endphp
                            @if($trashBadgeCount > 0)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $trashBadgeCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- PDF Catalog Link -->
                    <li class="nav-item">
                        <a class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1" href="{{ route('products.catalog-pdf') }}" target="_blank">
                            <i class="fas fa-file-pdf text-danger"></i> PDF Catalog
                        </a>
                    </li>

                    <!-- CSV Export Link -->
                    <li class="nav-item">
                        <a class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1" href="{{ route('products.export-csv') }}">
                            <i class="fas fa-file-csv text-success"></i> Export CSV
                        </a>
                    </li>

                    <!-- CSV Import Modal Trigger -->
                    <li class="nav-item">
                        <button type="button" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#importCsvModal">
                            <i class="fas fa-file-import text-info"></i> Import CSV
                        </button>
                    </li>

                    <!-- Add Product Button -->
                    <li class="nav-item">
                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3 fw-semibold">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </li>

                    <!-- Dark Mode / Light Mode Switcher -->
                    <li class="nav-item ms-lg-2">
                        <button type="button" id="themeToggleBtn" class="theme-toggle-btn" title="Toggle Dark/Light Mode">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main class="container-fluid px-lg-4 py-4">
        @yield('content')
    </main>

    <!-- Global CSV Import Modal -->
    <div class="modal fade" id="importCsvModal" tabindex="-1" aria-labelledby="importCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('products.import-csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="importCsvModalLabel">
                            <i class="fas fa-file-csv text-success me-2"></i> Bulk Import Products (CSV)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Upload a standard CSV file with headers: <code>Product Name, Price, SKU, Barcode, Stock Quantity, Description</code>. Products will be appended and ready for drag-and-drop reordering.
                        </p>
                        <div class="mb-3">
                            <label for="csv_file" class="form-label fw-semibold">Choose CSV File *</label>
                            <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv,text/csv" required>
                        </div>
                        <div class="bg-body-secondary p-2 rounded small">
                            <strong>Tip:</strong> You can download your current list via <strong>Export CSV</strong>, modify products, and re-import seamlessly.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-semibold">
                            <i class="fas fa-cloud-arrow-up me-1"></i> Upload &amp; Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container-fluid px-lg-4 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
            <div>
                <strong>Laravel 12 Product Master</strong> · SortableJS, Quick-Edit, Soft Deletes &amp; CSV/PDF Tools
            </div>
            <div>
                Built with Bootstrap 5.3 &amp; jQuery · Instant AJAX Sorting
            </div>
        </div>
    </footer>

    <!-- jQuery & Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global AJAX & Theme Setup -->
    <script>
        // Set AJAX CSRF Token Header
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // ----------------------------------------
        // Dark Mode / Light Mode Logic
        // ----------------------------------------
        (function() {
            const html = document.documentElement;
            const themeBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');

            // 1. Read stored preference or system default
            const storedTheme = localStorage.getItem('theme_mode');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const activeTheme = storedTheme ? storedTheme : (prefersDark ? 'dark' : 'light');

            applyTheme(activeTheme);

            function applyTheme(theme) {
                html.setAttribute('data-bs-theme', theme);
                if (theme === 'dark') {
                    themeIcon.className = 'fas fa-sun text-warning';
                } else {
                    themeIcon.className = 'fas fa-moon';
                }
                localStorage.setItem('theme_mode', theme);
            }

            if (themeBtn) {
                themeBtn.addEventListener('click', function() {
                    const current = html.getAttribute('data-bs-theme') || 'light';
                    const next = current === 'dark' ? 'light' : 'dark';
                    applyTheme(next);
                });
            }
        })();

        // Toast Helper
        window.showToast = function(msg, icon = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: msg,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        };

        // Flash Session Alerts
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>

    @stack('scripts')

</body>

</html>