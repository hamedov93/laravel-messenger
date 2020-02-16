<?php

namespace Hamedov\Messenger\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
    	'conversation_id', 'participant_id', 'message', 'read_by',
    ];

    public function conversation()
    {
    	return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }

    public function recepient()
    {
    	return $this->morphTo();
    }
}
