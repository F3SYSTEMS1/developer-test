<?php

namespace Tests\ControllersTests;

use Tests\TestCase;

class CsvExportTest extends TestCase
{
    /** @test */
    public function page_exists()
    {
        $page = $this->get('/')->assertStatus( 200);
        $page
            ->assertViewIs( 'home')
            ->assertSee('Developer test')
            ->assertSee('<c-s-v-generator>')
            ->assertSee('</c-s-v-generator>');
    }
}
