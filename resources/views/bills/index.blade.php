<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills List</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

<div class="page-header">
    <div class="header-inner">
        <div class="logo">&#9688; BillManager</div>
        <nav>
            <a href="{{ route('bills.create') }}" class="nav-link">New Bill</a>
            <a href="{{ route('bills.index') }}" class="nav-link active">Bills List</a>
        </nav>
    </div>
</div>

<div class="container">
    <div class="form-card">
        <div class="form-title">
            <h2>All Bills</h2>
            <p>Click on a bill to view its details</p>
        </div>

        @if($bills->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📄</div>
                <p>
                    No bills found.
                    <a href="{{ route('bills.create') }}">Create your first bill →</a>
                </p>
            </div>
        @else
        <div class="table-wrap">
            <table class="list-table">
                <thead>
                    <tr>
                        <th>Bill No</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Item Total</th>
                        <th>GST</th>
                        <th>Grand Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bills as $bill)
                    <tr>
                        <td>
                            <span class="bill-badge">{{ $bill->bill_no }}</span>
                        </td>
                        <td>{{ $bill->customer_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</td>
                        <td>₹ {{ number_format($bill->item_total, 2) }}</td>
                        <td>
                            {{ $bill->gst_percent }}%
                            (₹ {{ number_format($bill->gst_amount, 2) }})
                        </td>
                        <td>
                            <strong>₹ {{ number_format($bill->grand_total, 2) }}</strong>
                        </td>
                        <td>
                            <a href="{{ route('bills.show', $bill->id) }}" class="btn-view">
                                View
                            </a>
                            <a href="{{ route('bills.edit', $bill->id) }}"
                            class="btn-view"
                            style="margin-left:6px;">
                                Edit
                            </a>
                            <form action="{{ route('bills.destroy', $bill->id) }}"
                                method="POST"
                                style="display:inline-block"
                                onsubmit="return confirm('Are you sure you want to delete this bill?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn-view" style="margin-left:6px; border-color:#dc2626; color:#dc2626;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>

</body>
</html>