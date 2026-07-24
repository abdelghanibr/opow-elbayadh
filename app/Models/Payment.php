<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // اسم الجدول (اختياري إذا Laravel يستنتجه تلقائيًا)
    protected $table = 'payments';

    /**
     * الحقول القابلة للـ mass assignment
     */
    protected $fillable = [
        'order_id',
        'amount',
        'status',
        'payload','datetimesatim','updated_at'
    ];

    /**
     * Casts
     */
    protected $casts = [
        'payload' => 'array',
        'amount'  => 'decimal:2',
    ];

    /**
     * حالات الدفع (اختياري – للوضوح)
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED  = 'failed';

    /**
     * Scopes مفيدة
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
