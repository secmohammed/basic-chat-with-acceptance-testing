<?php

namespace App\Http\Controllers\Chat;

use App\Events\Chat\MessageCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreMessageRequest;
use App\Models\Chat\Message;

class ChatMessageController extends Controller {
	public function index() {
		$messages = Message::with('user')->latest()->limit(100)->get();
		return response()->json($messages, 200);
	}
	public function store(StoreMessageRequest $request) {
		$message = $request->user()->messages()->create([
			'body' => $request->get('body'),
		]);
		broadcast(new MessageCreated($message))->toOthers();
		return response()->json($message, 200);
	}
}
