<?php

namespace App\Events\Chat;

use App\Models\Chat\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageCreated implements ShouldBroadcast {
	use Dispatchable, InteractsWithSockets;
	public $message;

	public function __construct(Message $message) {
		$this->message = $message;
	}
	public function broadcastWith() {
		$this->message->load('user');
		return [
			'message' => array_merge($this->message->toArray(), [
				'selfOwned' => false,
			]),
		];
	}
	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn() {
		return new PresenceChannel('chat');
	}
}
