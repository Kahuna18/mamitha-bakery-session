<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'order_date', 'pickup_date',
        'type', 'status', 'notes', 'payment_proof',
        'payment_status', 'payment_method', 'snap_token', 'total', 'address',
        'latitude', 'longitude', 'google_maps_link', 'points_awarded'
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'pickup_date' => 'date',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::updated(function ($order) {
            if ($order->wasChanged('payment_status') && $order->payment_status === 'paid' && !$order->points_awarded) {
                $order->awardPointsToCustomer();
            }
        });
    }

    public function awardPointsToCustomer()
    {
        $customer = $this->customer;
        if ($customer && $customer->is_member && $customer->user_id !== null) {
            $pointsEarned = (int) floor($this->total / 10000);
            if ($pointsEarned > 0) {
                $oldRank = $customer->rank_name;
                $customer->increment('points', $pointsEarned);
                $newRank = $customer->fresh()->rank_name;

                // Flash points info to session for display on success page/modal
                session()->flash('points_earned', $pointsEarned);

                if ($newRank !== $oldRank) {
                    session()->flash('level_up', [
                        'old' => $oldRank,
                        'new' => $newRank,
                        'badge' => $customer->fresh()->rank_badge
                    ]);
                }
            }
        }
        $this->updateQuietly(['points_awarded' => true]);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function kitchenTask()
    {
        return $this->hasOne(KitchenTask::class);
    }

    public static function generateOrderNumber()
    {
        $prefix = 'MTH-' . date('Ymd');
        $last = self::where('order_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $num = (int) substr($last->order_number, -3) + 1;
        } else {
            $num = 1;
        }

        return $prefix . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function statusLabel()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'producing' => 'Sedang Dibuat',
            'ready' => 'Siap Diambil / Dikirim',
            'done' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function statusColor()
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'producing' => 'orange',
            'ready' => 'green',
            'done' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
