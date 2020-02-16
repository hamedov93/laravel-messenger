<?php

namespace Hamedov\Messenger\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
    	'conversation_id', 'messageable_id', 'messageable_type',
        'is_admin', 'status', 'last_read_message_id',
    ];

    public function conversation()
    {
    	return $this->belongsTo(Conversation::class);
    }

    public function messageable()
    {
        return $this->morphTo();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Add new message to conversation
     * @return [type] [description]
     */
    public function newMessage($message)
    {
        return $this->messages()->create([
            'conversation_id' => $this->conversation_id,
            'message' => $message,
            'read_by' => '{}',
        ]);
    }
}
