<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ChatPage;
use Tests\DuskTestCase;

class ChatRealTimeTest extends DuskTestCase {
	use DatabaseMigrations;
	/** @test */
	public function it_counts_correctly_the_current_online_users_as_someone_joins() {
		$users = factory(\App\Models\User::class, 2)->create();
		$this->browse(function ($browserOne, $browserTwo) use ($users) {
			$browserOne->loginAs($users->get(0))
				->visit(new ChatPage)
				->with('@onlineList', function ($online) use ($users) {
					$online->waitForText($users->get(0)->name);
					$online->assertSee($users->get(0)->name);
					$online->assertSee('1 user online');

				});
			$browserTwo->loginAs($users->get(1))
				->visit(new ChatPage)->waitFor('@onlineList');

			$browserOne->assertSeeIn('@onlineList', '2 users online');
		});

	}
	/** @test */
	public function it_counts_correctly_the_current_online_users_as_someone_leaves() {
		$users = factory(\App\Models\User::class, 2)->create();
		$this->browse(function ($browserOne, $browserTwo) use ($users) {
			$browserTwo->loginAs($users->get(1))
				->visit(new ChatPage);
			$browserOne->loginAs($users->get(0))
				->visit(new ChatPage)
				->with('@onlineList', function ($online) use ($users) {
					$online->waitForText($users->get(0)->name);
					$online->assertSee($users->get(0)->name);
					$online->assertSee('2 users online');

				});
			$browserTwo->logout();
			$browserOne->waitFor('@onlineList')->with('@onlineList', function ($online) use ($users) {
				$online->assertSee('1 user online');
			});
		});
	}
	/** @test */
	public function it_lists_the_current_online_users() {
		$users = factory(\App\Models\User::class, 2)->create();
		$this->browse(function ($browserOne, $browserTwo) use ($users) {
			$browserOne->loginAs($users->get(0))
				->visit(new ChatPage)
				->with('@onlineList', function ($online) use ($users) {
					$online->waitForText($users->get(0)->name);
					$online->assertSee($users->get(0)->name);
					$online->assertSee('1 user online');

				});
			$browserTwo->loginAs($users->get(1))
				->visit(new ChatPage)
				->with('@onlineList', function ($online) use ($users) {
					$online->waitForText($users->get(1)->name);
					$online->assertSee($users->get(1)->name);
					$online->assertSee('2 users online');

				});
		});
	}
	/** @test */
	public function it_allows_user_to_see_messages_from_other_users() {
		$users = factory(\App\Models\User::class, 3)->create();
		$this->browse(function ($browserOne, $browserTwo, $browserThree) use ($users) {
			$browserOne->loginAs($users->get(0))
				->visit(new ChatPage);
			$browserTwo->loginAs($users->get(1))
				->visit(new ChatPage);
			$browserThree->loginAs($users->get(2))
				->visit(new ChatPage);
			$browserOne->typeMessage('Hi there')
				->sendMessage();
			$browserThree->waitFor('@firstChatMessage')
				->with('@chatMessages', function ($messages) use ($users) {
					$messages->assertSee('Hi there')
						->assertSee($users->get(0)->name);
				});
			$browserTwo->waitFor('@firstChatMessage')
				->with('@chatMessages', function ($messages) use ($users) {
					$messages->assertSee('Hi there')
						->assertSee($users->get(0)->name);
				});
		});
	}
}
