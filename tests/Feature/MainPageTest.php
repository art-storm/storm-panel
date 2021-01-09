<?php

namespace Tests\Feature;

use Tests\TestCase;

class MainPageTest extends TestCase
{
    /**
     * Test open main page.
     *
     * @coversNothing
     * @return void
     */
    public function testMainPage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
