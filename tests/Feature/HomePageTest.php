<?php

namespace Tests\Feature;

use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(PortfolioSeeder::class);
    }

    public function test_the_tab_title_is_the_family_name_alone(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Malave</title>', false);
    }

    public function test_link_previews_use_the_wordmark_card(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Malave">', false)
            ->assertSee('<meta property="og:image" content="'.asset('og-image.png').'">', false);
    }
}
