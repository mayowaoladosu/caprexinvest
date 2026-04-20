<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ticket', 'subject', 'status', 'last_reply'];

    protected $dates = ['last_reply'];

    public function getUsernameAttribute()
    {
        return $this->name;
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function () {
            Cache::forget('ticketRecord');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages(){
        return $this->hasMany(SupportTicketMessage::class)->latest();
    }

    public function lastReply(){
        return $this->hasOne(SupportTicketMessage::class)->latest();
    }
    public function  getLastMessageAttribute(){
        return Str::limit($this->lastReply->message,40);
    }

    function getTicketStatusBadge() {
        switch ($this->status) {
            case 0:
                return '<span class="badge text-primary bg-soft-primary"> <span class="legend-indicator bg-primary"></span>' . __('Open') . '</span>';
            case 1:
                return '<span class="badge text-success bg-soft-success"> <span class="legend-indicator bg-success"></span>' . __('Answered') . '</span>';
            case 2:
                return '<span class="badge text-warning bg-soft-warning"><span class="legend-indicator bg-warning"></span>' . __('Replied') . '</span>';
            case 3:
                return '<span class="badge text-danger bg-soft-danger"><span class="legend-indicator bg-danger"></span>' . __('Closed') . '</span>';
            default:
                return '<span class="badge text-danger bg-soft-danger"><span class="legend-indicator bg-danger"></span>' . __('Unknown') . '</span>';
        }
    }

}
