<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bill</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

<div class="page-header">
    <div class="header-inner">
        <div class="logo">&#9688; BillManager</div>
        <nav>
            <a href="{{ route('bills.create') }}" class="nav-link">New Bill</a>
            <a href="{{ route('bills.index') }}" class="nav-link">Bills List</a>
        </nav>
    </div>
</div>

<div class="container">
    <div class="form-card">

        <!-- Header -->
        <div class="bill-view-header">
            <div>
                <h2>{{ $bill->bill_no }}</h2>
                <p>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</p>
            </div>

            <div class="bill-customer">
                <div class="label">Customer</div>
                <div class="name">{{ $bill->customer_name }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-wrap">
            <table class="list-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Price (₹)</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹ {{ number_format($item->price, 2) }}</td>
                        <td><strong>₹ {{ number_format($item->amount, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="totals-block">
            <div class="totals-inner">

                <div class="total-row">
                    <span>Item Total</span>
                    <strong>₹ {{ number_format($bill->item_total, 2) }}</strong>
                </div>

                <div class="total-row gst-row">
                    <span>GST ({{ $bill->gst_percent }}%)</span>
                    <strong>₹ {{ number_format($bill->gst_amount, 2) }}</strong>
                </div>

                <div class="total-row grand">
                    <span>Grand Total</span>
                    <strong>₹ {{ number_format($bill->grand_total, 2) }}</strong>
                </div>

            </div>
        </div>

        <!-- Footer Buttons -->
        <div class="form-footer">
            <a href="{{ route('bills.index') }}" class="btn-secondary">← Back</a>

            <button onclick="window.print()" class="btn-primary">
                Print
            </button>
        </div>

    </div>
</div>

</body>
</html>