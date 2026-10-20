<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Support\PortfolioCache;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PortfolioSeeder::class);
    }

    public function test_it_returns_every_section_in_one_response(): void
    {
        $response = $this->getJson('/api/portfolio');

        $response->assertOk()
            ->assertJsonStructure([
                'profile' => ['name', 'given_name', 'family_name', 'title', 'eyebrow', 'mailto', 'site_url', 'links', 'about', 'stats'],
                'experiences' => [['id', 'role', 'company', 'location', 'date_range', 'is_current', 'bullets']],
                'skill_groups' => [['name', 'is_highlighted', 'items' => [['id', 'name']]]],
                'projects' => [['id', 'title', 'description', 'stack', 'link', 'link_label']],
            ]);

        $this->assertCount(3, $response->json('experiences'));
        $this->assertCount(8, $response->json('skill_groups'));
        $this->assertCount(3, $response->json('projects'));
    }

    public function test_the_payload_is_not_wrapped_in_an_envelope(): void
    {
        $this->getJson('/api/portfolio')->assertJsonMissingPath('data');
    }

    public function test_a_null_end_date_renders_as_the_current_role(): void
    {
        $current = $this->getJson('/api/portfolio')->json('experiences.0');

        $this->assertSame('Aug 2022 – Present', $current['date_range']);
        $this->assertTrue($current['is_current']);
    }

    public function test_a_closed_role_renders_both_endpoints(): void
    {
        $closed = $this->getJson('/api/portfolio')->json('experiences.1');

        $this->assertSame('Mar 2022 – Dec 2022', $closed['date_range']);
        $this->assertFalse($closed['is_current']);
    }

    public function test_skills_arrive_grouped_with_geospatial_highlighted(): void
    {
        $groups = collect($this->getJson('/api/portfolio')->json('skill_groups'));

        $this->assertSame(
            ['Geospatial'],
            $groups->where('is_highlighted', true)->pluck('name')->all(),
        );
        $this->assertSame(
            ['Mapbox GL JS', 'QGIS', 'Map rendering', 'Geospatial data visualization'],
            collect($groups->firstWhere('name', 'Geospatial')['items'])->pluck('name')->all(),
        );
    }

    public function test_project_links_carry_a_prebuilt_host_label(): void
    {
        $projects = $this->getJson('/api/portfolio')->json('projects');

        $this->assertSame('northstarplan.app', $projects[0]['link_label']);
        $this->assertSame('trndecoin.com', $projects[1]['link_label']);
        $this->assertSame('trnde-videos-final.vercel.app', $projects[2]['link_label']);
    }

    public function test_a_project_without_a_link_has_no_label(): void
    {
        Project::query()->where('title', 'TrndeCoin')->update(['link' => null]);

        $project = $this->getJson('/api/portfolio')->json('projects.1');

        $this->assertNull($project['link']);
        $this->assertNull($project['link_label']);
    }

    public function test_profile_links_list_only_the_networks_that_are_set(): void
    {
        $this->assertSame([
            ['label' => 'GitHub', 'url' => 'https://github.com/emalave1030', 'display' => 'github.com/emalave1030'],
            ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/in/erwin-malave-89a20291', 'display' => 'linkedin.com/in/erwin-malave-89a20291'],
        ], $this->getJson('/api/portfolio')->json('profile.links'));

        Profile::query()->update(['github_url' => null]);
        Cache::forget(PortfolioCache::KEY);

        $this->assertSame(['LinkedIn'], array_column($this->getJson('/api/portfolio')->json('profile.links'), 'label'));
    }

    public function test_lists_honour_sort_order(): void
    {
        Experience::query()->where('company', 'Trnde LLC')->update(['sort_order' => 0]);

        $this->assertSame(
            'Trnde LLC',
            $this->getJson('/api/portfolio')->json('experiences.0.company'),
        );
    }

    public function test_the_payload_is_cached_and_reused(): void
    {
        Cache::forget(PortfolioCache::KEY);
        $this->assertFalse(Cache::has(PortfolioCache::KEY));

        $this->getJson('/api/portfolio')->assertOk();

        $this->assertTrue(Cache::has(PortfolioCache::KEY));
    }

    public function test_saving_content_invalidates_the_cache_and_the_next_read_is_fresh(): void
    {
        $this->getJson('/api/portfolio')->assertOk();

        Skill::query()->where('name', 'QGIS')->first()->update(['name' => 'QGIS 3']);

        $this->assertFalse(Cache::has(PortfolioCache::KEY));

        $names = collect($this->getJson('/api/portfolio')->json('skill_groups'))
            ->firstWhere('name', 'Geospatial')['items'];

        $this->assertContains('QGIS 3', array_column($names, 'name'));
    }

    public function test_deleting_content_invalidates_the_cache(): void
    {
        $this->getJson('/api/portfolio')->assertOk();

        Skill::query()->where('name', 'QGIS')->first()->delete();

        $this->assertFalse(Cache::has(PortfolioCache::KEY));
        $this->assertCount(3, collect($this->getJson('/api/portfolio')->json('skill_groups'))
            ->firstWhere('name', 'Geospatial')['items']);
    }
}
