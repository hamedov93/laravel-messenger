<?php

namespace Hamedov\Messenger\Traits;

use Hamedov\Messenger\Models\Conversation;

Trait Relatable {

	public function conversations()
	{
		return $this->morphMany(Conversation::class, 'relatable');
	}
}
