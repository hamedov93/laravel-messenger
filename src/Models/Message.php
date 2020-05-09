<?php

namespace Hamedov\Messenger\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Message extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = [
    	'conversation_id', 'participant_id', 'message',
        'read_by', 'type',
    ];

    public function registerMediaCollections()
    {
        $this->addMediaCollection(config('messaging.images_collection', 'messages'))
            ->registerMediaConversions(function ($media) {
                $conversions = config('messaging.image_conversions', ['thumb' => [300, 300]]);
                foreach ($conversions as $key => $value)
                {
                    $this->addMediaConversion($key)
                        ->width($value[0])
                        ->height($value[1]);
                }
            });
    }

    public function conversation()
    {
    	return $this->belongsTo(Conversation::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function recepients()
    {
        return $this->hasMany(Participant::class, 'conversation_id', 'conversation_id')
            ->where('participants.id', '!=', $this->participant_id);
    }
}
