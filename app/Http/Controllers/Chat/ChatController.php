<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

class ChatController extends Controller {
	public function __construct() {
		$this->middleware('auth');
	}
	public function index() {
		return view('chat.index');
	}
}
