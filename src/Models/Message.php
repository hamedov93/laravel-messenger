<?php

namespace Hamedov\Messenger\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Message extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
    	'conversation_id', 'participant_id', 'message',
        'read_by', 'type',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(config('messaging.images_collection', 'messages'));
    }

    /**
     * Register media conversions
     * @return void
     */
    public function registerMediaConversions(BaseMedia $media = null): void
    {
        $conversions = config('messaging.image_conversions', ['thumbnail' => [300, 300]]);
        foreach ($conversions as $key => $value) {
            $this->addMediaConversion($key)
                ->fit(Manipulations::FIT_MAX, $value[0], $value[1]);
        }
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
