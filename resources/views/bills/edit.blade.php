<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bill</title>
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
        <div class="form-title">
            <h2>Edit Bill</h2>
            <p>Update billing details</p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success">
                ✔ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ✖ {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('bills.update', $bill->id) }}" id="billForm">
            @csrf
            @method('PUT')

            <!-- Bill Info -->
            <div class="section-label">Bill Information</div>
            <div class="row-3">

                <div class="field">
                    <label>Bill No</label>
                    <input type="text" value="{{ $bill->bill_no }}" readonly>
                </div>

                <div class="field">
                    <label>Customer Name <span class="req">*</span></label>
                    <input type="text" name="customer_name"
                           value="{{ old('customer_name', $bill->customer_name) }}" required>
                </div>

                <div class="field">
                    <label>Bill Date <span class="req">*</span></label>
                    <input type="date" name="bill_date"
                           value="{{ old('bill_date', $bill->bill_date) }}" required>
                </div>

            </div>

            <!-- Items -->
            <div class="section-label">
                Items
                <button type="button" class="btn-add" onclick="addRow()">+ Add Item</button>
            </div>

            <div class="table-wrap">
                <table id="itemTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name *</th>
                            <th>Quantity *</th>
                            <th>Price (₹) *</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody id="itemBody">
                        @foreach($bill->items as $i => $item)
                        <tr class="item-row">
                            <td class="row-num">{{ $i + 1 }}</td>

                            <td>
                                <input type="text" name="item_name[]"
                                       value="{{ $item->item_name }}" required>
                            </td>

                            <td>
                                <input type="number" name="quantity[]"
                                       value="{{ $item->quantity }}"
                                       min="1" required oninput="calcRow(this)">
                            </td>

                            <td>
                                <input type="number" name="price[]"
                                       value="{{ $item->price }}"
                                       step="0.01" required oninput="calcRow(this)">
                            </td>

                            <td class="amount-cell">
                                <span class="amount-val">
                                    {{ number_format($item->amount, 2) }}
                                </span>
                            </td>

                            <td>
                                <button type="button" class="btn-del" onclick="removeRow(this)">✖</button>
                            </td>
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
                        <strong id="itemTotalDisplay">₹ 0.00</strong>
                    </div>

                    <div class="total-row gst-row">
                        <span>GST %</span>
                        <div class="gst-inputs">
                            <input type="number" name="gst_percent" id="gstPercent"
                                   value="{{ old('gst_percent', $bill->gst_percent) }}"
                                   oninput="calcTotals()">
                            <strong id="gstAmountDisplay">₹ 0.00</strong>
                        </div>
                    </div>

                    <div class="total-row grand">
                        <span>Grand Total</span>
                        <strong id="grandTotalDisplay">₹ 0.00</strong>
                    </div>

                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('bills.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Bill</button>
            </div>

        </form>
    </div>
</div>

<script>
let rowCount = document.querySelectorAll('.item-row').length;

function addRow() {
    rowCount++;
    const tbody = document.getElementById('itemBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';

    tr.innerHTML = `
        <td class="row-num">${rowCount}</td>
        <td><input type="text" name="item_name[]" required></td>
        <td><input type="number" name="quantity[]" min="1" required oninput="calcRow(this)"></td>
        <td><input type="number" name="price[]" step="0.01" required oninput="calcRow(this)"></td>
        <td><span class="amount-val">0.00</span></td>
        <td><button type="button" class="btn-del" onclick="removeRow(this)">✖</button></td>
    `;
    tbody.appendChild(tr);
}

function removeRow(btn) {
    if (document.querySelectorAll('.item-row').length === 1) {
        alert('At least one item required');
        return;
    }
    btn.closest('tr').remove();
    renumber();
    calcTotals();
}

function renumber() {
    document.querySelectorAll('.item-row').forEach((tr, i) => {
        tr.querySelector('.row-num').textContent = i + 1;
    });
    rowCount = document.querySelectorAll('.item-row').length;
}

function calcRow(input) {
    const tr = input.closest('tr');
    const qty = parseFloat(tr.querySelector('input[name="quantity[]"]').value) || 0;
    const price = parseFloat(tr.querySelector('input[name="price[]"]').value) || 0;
    tr.querySelector('.amount-val').textContent = (qty * price).toFixed(2);
    calcTotals();
}

function calcTotals() {
    let total = 0;
    document.querySelectorAll('.amount-val').forEach(el => {
        total += parseFloat(el.textContent) || 0;
    });

    const gst = parseFloat(document.getElementById('gstPercent').value) || 0;
    const gstAmt = total * gst / 100;
    const grand = total + gstAmt;

    document.getElementById('itemTotalDisplay').textContent = '₹ ' + total.toFixed(2);
    document.getElementById('gstAmountDisplay').textContent = '₹ ' + gstAmt.toFixed(2);
    document.getElementById('grandTotalDisplay').textContent = '₹ ' + grand.toFixed(2);
}

// init totals on load
calcTotals();
</script>

</body>
</html>