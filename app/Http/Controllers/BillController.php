<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bill;
use App\Models\BillItem;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::latest()->get(); // same as ORDER BY created_at DESC
        return view('bills.index', compact('bills'));
    }
    public function create_old()
    {
        return view('bills.create');
    }
    public function create()
    {
        $last = Bill::latest()->first();
        $nextId = $last ? $last->id + 1 : 1;

        $bill_no = 'BILL-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('bills.create', compact('bill_no'));
    }
        public function store(Request $request)
    {
        $request->validate([
            'bill_no' => 'required|unique:bills',
            'customer_name' => 'required',
            'bill_date' => 'required|date',
            'item_name.*' => 'required',
            'quantity.*' => 'required|numeric|min:1',
            'price.*' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $item_total = 0;
            $items = [];

            foreach ($request->item_name as $i => $name) {
                $qty = $request->quantity[$i];
                $price = $request->price[$i];

                $amount = $qty * $price;
                $item_total += $amount;

                $items[] = [
                    'item_name' => $name,
                    'quantity' => $qty,
                    'price' => $price,
                    'amount' => $amount
                ];
            }

            $gst_percent = $request->gst_percent ?? 0;
            $gst_amount = ($item_total * $gst_percent) / 100;
            $grand_total = $item_total + $gst_amount;

            $bill = Bill::create([
                'bill_no' => $request->bill_no,
                'customer_name' => $request->customer_name,
                'bill_date' => $request->bill_date,
                'item_total' => $item_total,
                'gst_percent' => $gst_percent,
                'gst_amount' => $gst_amount,
                'grand_total' => $grand_total,
            ]);

            foreach ($items as $item) {
                $bill->items()->create($item);
            }

            DB::commit();

            return redirect()->route('bills.create')->with('success', 'Bill saved successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }
    public function store_old(Request $request)
    {
        $request->validate([
            'bill_no' => 'required|unique:bills',
            'customer_name' => 'required',
            'bill_date' => 'required|date',
            'items.*.item_name' => 'required',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $itemTotal = 0;

            foreach ($request->items as $item) {
                $itemTotal += $item['quantity'] * $item['price'];
            }

            $gstPercent = $request->gst_percent ?? 0;
            $gstAmount = ($itemTotal * $gstPercent) / 100;
            $grandTotal = $itemTotal + $gstAmount;

            $bill = Bill::create([
                'bill_no' => $request->bill_no,
                'customer_name' => $request->customer_name,
                'bill_date' => $request->bill_date,
                'item_total' => $itemTotal,
                'gst_percent' => $gstPercent,
                'gst_amount' => $gstAmount,
                'grand_total' => $grandTotal,
            ]);

            foreach ($request->items as $item) {
                $bill->items()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Bill saved!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $bill = Bill::with('items')->findOrFail($id);
        return view('bills.show', compact('bill'));
    }
    public function edit($id)
    {
        $bill = Bill::with('items')->findOrFail($id);
        return view('bills.edit', compact('bill'));
    }
    public function update(Request $request, $id)
    {
        $bill = Bill::findOrFail($id);

        $request->validate([
            'customer_name' => 'required',
            'bill_date' => 'required|date',
            'item_name.*' => 'required',
            'quantity.*' => 'required|numeric|min:1',
            'price.*' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $item_total = 0;
            $items = [];

            foreach ($request->item_name as $i => $name) {
                $qty = $request->quantity[$i];
                $price = $request->price[$i];

                $amount = $qty * $price;
                $item_total += $amount;

                $items[] = [
                    'item_name' => $name,
                    'quantity' => $qty,
                    'price' => $price,
                    'amount' => $amount
                ];
            }

            $gst_percent = $request->gst_percent ?? 0;
            $gst_amount = ($item_total * $gst_percent) / 100;
            $grand_total = $item_total + $gst_amount;

            // update bill
            $bill->update([
                'customer_name' => $request->customer_name,
                'bill_date' => $request->bill_date,
                'item_total' => $item_total,
                'gst_percent' => $gst_percent,
                'gst_amount' => $gst_amount,
                'grand_total' => $grand_total,
            ]);

            // delete old items
            $bill->items()->delete();

            // insert new items
            foreach ($items as $item) {
                $bill->items()->create($item);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Bill updated!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $bill = Bill::findOrFail($id);

        // items will auto delete if you used cascadeOnDelete in migration
        $bill->delete();

        return redirect()->route('bills.index')
            ->with('success', 'Bill deleted successfully!');
    }
}
