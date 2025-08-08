<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreConnectionPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function un_usuario_autenticado_puede_ver_la_pagina_de_conectar_tienda(): void
    {
        // 1. Preparación
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Acción
        $response = $this->get(route('store.create'));

        // 3. Aserciones
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Store/Create')
        );
    }

    #[Test]
    public function un_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        // 1. Acción
        $response = $this->get(route('store.create'));

        // 2. Aserciones
        $response->assertRedirect(route('login'));
    }
}
