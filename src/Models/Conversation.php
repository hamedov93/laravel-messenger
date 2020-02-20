<?php

namespace Hamedov\Messenger\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
    	'name', 'relatable_id', 'relatable_type',
    ];

    /**
     * Get the model related to the conversation
     * eg. Post, Ad, Article etc.
     * @return [type] [description]
     */
    public function relatable()
    {
    	return $this->morphTo();
    }

    /**
     * Get conversation messages
     * @return [type] [description]
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get conversation latest message
     * @return [type] [description]
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Get conversation participant entries in participants table
     * And not the actual participant models.
     * This is used along with messageable relationship in
     * Participant model to get all participants at once.
     * As The builder only allows getting one participant type/model
     * at a time using morphedByMany
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get conversation participants/messageables of one type directly
     * This is the problem we have solved using the previous method
     */
    public function messageables($messageable_model = 'App\User')
    {
        if ( ! class_exists($messageable_model))
        {
            return null;
        }

        return $this->morphedByMany($messageable_model, 'messageable', 'participants', 'conversation_id', 'messageable_id');
    }

    /**
     * Add new message to a conversation
     */
    public function newMessage(Model $sender, $message)
    {
        $participant = $this->getParticipant($sender);
        return $participant->newMessage($message);
    }

    /**
     * Get conversation participant by model
     */
    public function getParticipant(Model $model)
    {
        return $this->participants()->where([
            'messageable_id' => $model->id,
            'messageable_type' => $model->getMorphClass(),
        ])->first();
    }
    
    /**
     * Add new participant to conversation
     */
    public function addParticipant(Model $model, $is_admin = false)
    {
        return $this->participants()->firstOrCreate([
            'messageable_id' => $model->id,
            'messageable_type' => $model->getMorphClass(),
            'is_admin' => $is_admin ? '1' : '0',
        ]);
    }

    /**
     * Check if specific entity has joined a conversation
     */
    public function hasParticipant(Model $model)
    {
        return (int) $this->participants()->where([
            'messageable_id' => $model->id,
            'messageable_type' => $model->getMorphClass(),
        ])->count() > 0;
    }
}
