<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index() {
        $invoice = Invoice::latest()->get();

        return response()->json($invoice);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'number' => 'required|string|unique:invoices,number',
            'supplier_name' => 'required|string',
            'supplier_tax_id' => 'required|string',
            'net_amount' => 'required|numeric|gt:0',
            'vat_amount' => 'required|numeric|min:0',
            'gross_amount' => 'required|numeric',
            'currency' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        if ($data['gross_amount'] != ($data['net_amount'] + $data['vat_amount'])) {
            return response()->json(['message' => 'Gross amount must equal Net + VAT'], 422);
        }

        return Invoice::create($data);
    }

    // Один конкретный счет
    public function show(Invoice $invoice) {
        return $invoice;
    }

    // Обновление
    public function update(Request $request, Invoice $invoice) {
        // Обновление разрешено только для статуса pending
        if ($invoice->status !== 'pending') {
            return response()->json(['message' => 'Only pending invoices can be updated'], 403);
        }

        $data = $request->validate([
            'net_amount' => 'required|numeric|gt:0',
            'vat_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:issue_date',
            // Другие поля...
        ]);

        // Перевалидация сумм при обновлении
        $invoice->update($request->all());
        return $invoice;
    }

    // Удаление
    public function destroy(Invoice $invoice) {
        $invoice->delete();
        return response()->noContent();
    }
}
