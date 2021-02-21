<?php

namespace Tests\ControllersTests;

use Tests\TestCase;

class FrontendTests extends TestCase
{
    /**
     * Check of accessibility and present some essential structure blocks of "Home" page.
     *
     * @test
     * @return void
     */
    public function pageExistCheck()
    {
        $page = $this->get('/')->assertStatus( 200);
        $page
            ->assertViewIs( 'home')
            ->assertSee('Developer test')
            ->assertSee('<c-s-v-generator>')
            ->assertSee('</c-s-v-generator>');
    }
}
