<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'orderable_type',
        'orderable_id',
        'user_id',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'amount',
        'status',
        'snap_token',
        'payment_type',
        'download_token',
        'account_provisioned_at',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'account_provisioned_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
    }

    public static function generateDownloadToken(): string
    {
        return Str::random(40);
    }

    /**
     * URL halaman produk ini (fallback kalau tombol "Lanjutkan Bayar" langsung tidak dipakai).
     */
    public function productUrl(): ?string
    {
        return match ($this->orderable_type) {
            Prompt::class => $this->orderable ? route('products.prompt.show', $this->orderable) : null,
            default => null,
        };
    }

    /**
     * Slug tipe produk yang dipakai di URL checkout (/checkout/{type}/{slug}),
     * dipakai tombol "Lanjutkan Bayar" untuk langsung buka Snap tanpa pindah halaman.
     */
    public function productType(): ?string
    {
        return match ($this->orderable_type) {
            Prompt::class => 'prompt',
            default => null,
        };
    }
}
