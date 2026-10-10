<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.monetbil.service_key' => 'TEST_SERVICE_KEY']);
        config(['services.monetbil.service_secret' => 'TEST_SERVICE_SECRET']);
    }

    public function test_guest_is_redirected_to_login_when_checkout_attempted(): void
    {
        $book = Book::factory()->create([
            'price' => 5000,
            'is_published' => true,
        ]);

        $response = $this->get(route('payments.checkout', $book->slug));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_checkout(): void
    {
        $user = User::factory()->create(['type_id' => 1]);
        $book = Book::factory()->create([
            'price' => 3500,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)->get(route('payments.checkout', $book->slug));

        $response->assertStatus(200);
        $response->assertSee('Pay by Mobile Money');
        $response->assertSee('TEST_SERVICE_KEY');

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'amount' => 3500,
            'status' => 'pending',
        ]);
    }

    public function test_already_purchased_book_redirects_to_download(): void
    {
        $user = User::factory()->create(['type_id' => 1]);
        $book = Book::factory()->create(['price' => 2500, 'is_published' => true]);

        Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'amount' => 2500,
            'status' => 'success',
            'payment_ref' => 'PAY-TEST-123456',
        ]);

        $response = $this->actingAs($user)->get(route('payments.checkout', $book->slug));

        $response->assertRedirect(route('books.download', $book->slug));
    }

    public function test_monetbil_ipn_notification_updates_payment(): void
    {
        $user = User::factory()->create(['type_id' => 1]);
        $book = Book::factory()->create(['price' => 4000]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'amount' => 4000,
            'status' => 'pending',
            'payment_ref' => 'PAY-REF-999',
        ]);

        $response = $this->postJson(route('payments.notify'), [
            'status' => 'SUCCESS',
            'item_ref' => 'PAY-REF-999',
            'transaction_id' => 'TXN-MONETBIL-888',
            'phone' => '237699999999',
            'operator' => 'CM_ORANGEMONEY',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
            'transaction_id' => 'TXN-MONETBIL-888',
            'phone_number' => '237699999999',
            'operator' => 'CM_ORANGEMONEY',
        ]);
    }

    public function test_payment_return_redirects_to_my_books(): void
    {
        $user = User::factory()->create(['type_id' => 1]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => Book::factory()->create()->id,
            'amount' => 3000,
            'status' => 'pending',
            'payment_ref' => 'PAY-RET-101',
        ]);

        $response = $this->actingAs($user)->get(route('payments.return', ['payment_ref' => 'PAY-RET-101']));

        $response->assertRedirect(route('my-books'));
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
        ]);
    }

    public function test_purchased_books_appear_in_my_books(): void
    {
        $user = User::factory()->create(['type_id' => 1]);
        $category = Category::factory()->create();
        $book = Book::factory()->create(['title' => 'Livre Achete Sur Mes Livres', 'category_id' => $category->id]);

        Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'amount' => 5000,
            'status' => 'success',
            'payment_ref' => 'PAY-MYBOOKS-1',
        ]);

        $response = $this->actingAs($user)->get(route('my-books'));

        $response->assertStatus(200);
        $response->assertSee('Livre Achete Sur Mes Livres');
    }
}
