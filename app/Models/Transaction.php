<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $fillable = [
        'kasir_id',
        'member_id',
        'total',
        'status',
        'transaction_date',
        'send_whatsapp_notification',
        'payment_method',
        'cash',
        'change',
        'point_usage',
        'reward_point'
    ];

    public $timestamps = false;

    const STATUS_PAID = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';

    const PAYMENT_METHOD_CASH = 'tunai';
    const PAYMENT_METHOD_TRANSFER = 'transfer';
    const PAYMENT_METHOD_QRIS = 'qris';

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'total' => 'decimal:2',
            'send_whatsapp_notification' => 'boolean',
        ];
    }

    public function getInvoiceNumberAttribute()
    {
        $formatId = str_pad($this->id, 3, '0', STR_PAD_LEFT);
        return 'Order#' . $formatId;
    }

    public function getStrukUrlAttribute()
    {
        return route('struk.search', ['invoice' => $this->invoice_number]);
    }

    public function scopeFindByInvoice(Builder $query, string $invoice)
    {
        // Coba ambil angka setelah "Order#"
        if (preg_match('/Order#(\d+)/', $invoice, $matches)) {
            $transactionId = (int) $matches[1]; // Ambil ID transaksi

            return $query->where('id', $transactionId);
        }

        return $query; // Jika format salah, kembalikan query tanpa filter
    }

    /**
     * Get the kasir that owns the transaction.
     */
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    /**
     * Get the member that owns the transaction.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
