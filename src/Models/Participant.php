<?php

namespace Hamedov\Messenger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Lang;

class Participant extends Model
{
    protected $fillable = [
    	'conversation_id', 'messageable_id', 'messageable_type',
        'is_admin', 'status', 'last_read',
    ];

    protected $dates = [
        'last_read',
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
     * 
     * @return \Hamedov\Messenger\Models\Message
     */
    public function newMessage($message, $type = null)
    {
        $text = $this->getMessageText($message);
        $type = $type ?? $this->getMessageType($message);
        $msg = $this->messages()->create([
            'conversation_id' => $this->conversation_id,
            'message' => $text,
            'type' => $type,
            'read_by' => '{}',
        ]);

        return $this->addMessageMedia($msg, $message);
    }

    public function getMessageText($message)
    {
        if ($message instanceof UploadedFile)
        {
            return Lang::get('Photo');
        }

        if (is_array($message))
        {
            return count($message) . ' ' . Lang::get('Photos');
        }

        return $message;
    }

    public function getMessageType($message)
    {
        return is_array($message) || $message instanceof UploadedFile
            ? 'media'
            : 'text';
    }

    public function addMessageMedia($message, $files)
    {
        if ($files instanceof UploadedFile)
        {
            $files = [$files];
        }

        if ( ! is_array($files))
        {
            return $message;
        }

        foreach ($files as $file)
        {
            $message->addMedia($file)->toMediaCollection(config('messaging.images_collection'));
        }

        return $message;
    }

    public function scopeMessageable($query, $messageable)
    {
        $query->where('participants.messageable_id', $messageable->getKey());
        $query->where('participants.messageable_type', $messageable->getMorphClass());
    }

    public function scopeOtherThan($query, $messageable)
    {
        $query->where(function($q) {
            $query->where('participants.messageable_id', '!=', $messageable->getKey());
            $query->orWhere('participants.messageable_type', '!=', $messageable->getMorphClass());
        });
    }
}
