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

    /**
     * Register media collections
     * @return void
     */
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
        $conversions = config('messaging.image_conversions', [
            'small' => [
                'fit_mode' => Manipulations::FIT_CROP,
                'width' => 200,
                'height' =>200,
            ],
            'medium' => [
                'fit_mode' => Manipulations::FIT_CROP,
                'width' => 360,
                'height' => 360,
            ],
            'large' => [
                'fit_mode' => Manipulations::FIT_MAX,
                'width' => 600,
                'height' => 600,
            ],
        ]);

        foreach ($conversions as $name => $config) {
            $this->addMediaConversion($name)
                ->fit(
                    $config['fit_mode'] ?? Manipulations::FIT_MAX,
                    $config['width'],
                    $config['height']
                );
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
