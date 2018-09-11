<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ChatPage;
use Tests\DuskTestCase;

class ChatTest extends DuskTestCase {
	use DatabaseMigrations;

	/** @test */
	public function it_lets_user_send_a_message() {
		$user = factory(\App\Models\User::class)->create();
		$this->browse(function (Browser $browser) use ($user) {
			$browser
				->loginAs($user)
				->visit(new ChatPage)
				->typeMessage('Hi There')
				->sendMessage()
				->assertInputValue('@body', '')
				->with('@chatMessages', function ($messages) use ($user) {
					$messages->assertSee('Hi There')->assertSee($user->name);
				})
				->logout();
		});
	}
	/** @test */
	public function it_lets_user_send_a_multiline_message() {
		$user = factory(\App\Models\User::class)->create();
		$this->browse(function (Browser $browser) use ($user) {
			$browser
				->loginAs($user)
				->visit(new ChatPage)
				->typeMessage('Test Message')
				->keys('@body', '{shift}', '{enter}')
				->append('@body', 'New line')
				->sendMessage()
				->assertSeeIn('@chatMessages', "Test Message\nNew line")
				->logout();
		});
	}
	/** @test */
	public function it_cannot_let_user_send_an_empty_message() {
		$user = factory(\App\Models\User::class)->create();
		$this->browse(function (Browser $browser) use ($user) {
			$browser
				->loginAs($user)
				->visit(new ChatPage);
			foreach (['    ', ''] as $value) {
				$browser->typeMessage($value)
					->sendMessage()
					->assertDontSeeIn('@chatMessages', $user->name);
			}
			$browser->keys('@body', '{shift}', '{enter}')
				->keys('@body', '{shift}', '{enter}')
				->sendMessage()
				->assertDontSeeIn('@chatMessages', $user->name);
		});
	}
	/** @test */
	public function it_orders_messages_by_latest() {
		$user = factory(\App\Models\User::class)->create();
		$this->browse(function (Browser $browser) use ($user) {
			$browser
				->loginAs($user)
				->visit(new ChatPage);
			foreach (['One', 'Two', 'There'] as $message) {
				$browser->typeMessage($message)
					->sendMessage()
					->waitFor('@firstChatMessage')
					->assertSeeIn('@firstChatMessage', $message);
			}
		});
	}
	/** @test */
	public function it_user_has_his_messages_highlighted() {
		$user = factory(\App\Models\User::class)->create();
		$this->browse(function (Browser $browser) use ($user) {
			$browser->loginAs($user)
				->visit(new ChatPage)
				->typeMessage('My Message')
				->sendMessage()
				->waitFor('@ownMessage')
				->with('@ownMessage', function ($message) use ($user) {
					$message->assertSee('My Message')
						->assertSee($user->name);
				});
		});
	}
}
