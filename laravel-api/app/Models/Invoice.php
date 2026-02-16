<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'number', 'supplier_name', 'supplier_tax_id',
        'net_amount', 'vat_amount', 'gross_amount',
        'currency', 'status', 'issue_date', 'due_date'
    ];
}
