<?php

use App\Models\User;

test('login page renders with Prodiakon brand', function () {
    $this->get('/admin/login')->assertOk()->assertSee('Prodiakon');
});

test('dashboard shows hero, indonesian menu and sidebar user card', function () {
    $user = User::factory()->create(['name' => 'Matius Kristyanto']);

    $this->actingAs($user)->get('/admin')
        ->assertOk()
        ->assertSee('Selamat datang')
        ->assertSee('Matius')
        ->assertSee('Dasbor')
        ->assertSee('Keluar');
});
