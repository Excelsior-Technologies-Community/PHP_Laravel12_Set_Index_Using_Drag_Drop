<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Catalog - {{ $generatedAt }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
        }
        .meta {
            color: #666;
            font-size: 11px;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #F1F5F9;
            color: #1E293B;
            font-size: 11px;
            text-transform: uppercase;
            padding: 8px 10px;
            border-bottom: 1px solid #CBD5E1;
            text-align: left;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #E2E8F0;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-success { background: #DCFCE7; color: #166534; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .sku { font-family: monospace; font-size: 11px; color: #475569; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #94A3B8;
            font-size: 10px;
            border-top: 1px solid #E2E8F0;
            padding-top: 10px;
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom:15px; text-align:right;">
        <button onclick="window.print()" style="padding:8px 16px; background:#4F46E5; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <div class="header">
        <div>
            <div class="title">Product Catalog &amp; Inventory</div>
            <div style="color:#64748B; margin-top:2px;">Ordered by Custom Drag &amp; Drop Sequence</div>
        </div>
        <div class="meta">
            <div><strong>Generated:</strong> {{ $generatedAt }}</div>
            <div><strong>Total Items:</strong> {{ $products->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="40">#</th>
                <th width="100">SKU</th>
                <th>Product Name &amp; Details</th>
                <th width="80" style="text-align:right;">Price</th>
                <th width="90">Stock</th>
                <th width="70">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td><strong>#{{ $product->sort_order }}</strong></td>
                    <td class="sku">{{ $product->sku ?? 'N/A' }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->description)
                            <div style="color:#64748B; font-size:11px; margin-top:2px;">{{ Str::limit($product->description, 80) }}</div>
                        @endif
                        @if($product->barcode)
                            <div style="font-family:monospace; font-size:9.5px; color:#94A3B8;">Barcode: {{ $product->barcode }}</div>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        @if($product->has_discount)
                            <span style="color:#DC2626; font-weight:bold;">${{ number_format($product->discount_price, 2) }}</span>
                            <div style="text-decoration:line-through; color:#94A3B8; font-size:10px;">${{ number_format($product->price, 2) }}</div>
                        @else
                            <strong>${{ number_format($product->price, 2) }}</strong>
                        @endif
                    </td>
                    <td>
                        @if($product->stock_status === 'in_stock')
                            <span class="badge badge-success">{{ $product->stock_quantity }} In Stock</span>
                        @elseif($product->stock_status === 'low_stock')
                            <span class="badge badge-warning">Low: {{ $product->stock_quantity }}</span>
                        @else
                            <span class="badge badge-danger">Out of Stock</span>
                        @endif
                    </td>
                    <td>
                        @if($product->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Draft</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laravel 12 Product Master Catalog · Generated on {{ $generatedAt }}
    </div>

</body>
</html>
