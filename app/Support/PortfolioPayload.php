<?php

namespace App\Support;

use App\Models\Concerns\ClearsPortfolioCache;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Support\Facades\Cache;

/**
 * Builds the single aggregate served by GET /api/portfolio.
 *
 * The payload is shaped here, once, in the exact form the page renders:
 * dates are already formatted, skills are already grouped, links are already
 * absolute. The frontend maps over arrays and prints strings — nothing else.
 */
final class PortfolioPayload
{
    /**
     * Cached forever; the four models drop the entry whenever content changes.
     *
     * @see ClearsPortfolioCache
     */
    public function cached(): array
    {
        return Cache::rememberForever(PortfolioCache::KEY, fn (): array => $this->build());
    }

    public function build(): array
    {
        return [
            'profile' => $this->profile(),
            'experiences' => $this->experiences(),
            'skill_groups' => $this->skillGroups(),
            'projects' => $this->projects(),
        ];
    }

    private function profile(): ?array
    {
        $profile = Profile::query()->first();

        if ($profile === null) {
            return null;
        }

        return [
            'name' => $profile->name,
            'given_name' => $profile->given_name,
            'family_name' => $profile->family_name,
            'title' => $profile->title,
            'location' => $profile->location,
            'languages' => $profile->languages,
            'eyebrow' => $profile->location.' · '.$profile->languages,
            'email' => $profile->email,
            'mailto' => 'mailto:'.$profile->email,
            'site' => $profile->site,
            'site_url' => 'https://'.$profile->site,
            'links' => $this->profileLinks($profile),
            'about' => $profile->about,
            'stats' => $profile->stats,
        ];
    }

    /**
     * Only the profiles that are filled in, labelled, so the page can loop
     * over them without knowing which networks exist. The display form drops
     * the scheme so it still reads as an address on the printed résumé.
     *
     * @return list<array{label: string, url: string, display: string}>
     */
    private function profileLinks(Profile $profile): array
    {
        return collect([
            'GitHub' => $profile->github_url,
            'LinkedIn' => $profile->linkedin_url,
        ])
            ->filter()
            ->map(fn (string $url, string $label): array => [
                'label' => $label,
                'url' => $url,
                'display' => rtrim(preg_replace('#^https?://(www\.)?#', '', $url), '/'),
            ])
            ->values()
            ->all();
    }

    private function experiences(): array
    {
        return Experience::query()
            ->ordered()
            ->get()
            ->map(fn (Experience $experience): array => [
                'id' => $experience->id,
                'role' => $experience->role,
                'company' => $experience->company,
                'location' => $experience->location,
                'date_range' => $experience->date_range,
                'is_current' => $experience->is_current,
                'bullets' => $experience->bullets,
            ])
            ->all();
    }

    /**
     * Grouped server-side so the page can render clusters straight from the
     * payload. A group is highlighted when any skill in it is.
     */
    private function skillGroups(): array
    {
        return Skill::query()
            ->ordered()
            ->get()
            ->groupBy('group')
            ->map(fn ($skills, string $group): array => [
                'name' => $group,
                'is_highlighted' => $skills->contains(fn (Skill $skill): bool => $skill->is_highlighted),
                'items' => $skills
                    ->map(fn (Skill $skill): array => ['id' => $skill->id, 'name' => $skill->name])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    private function projects(): array
    {
        return Project::query()
            ->ordered()
            ->get()
            ->map(fn (Project $project): array => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'stack' => $project->stack,
                'link' => $project->link,
                'link_label' => $this->linkLabel($project->link),
            ])
            ->all();
    }

    /**
     * The bare host, so the card can show where a link goes without the
     * frontend picking apart URLs.
     */
    private function linkLabel(?string $link): ?string
    {
        if ($link === null) {
            return null;
        }

        $host = parse_url($link, PHP_URL_HOST);

        return is_string($host) ? preg_replace('/^www\./', '', $host) : $link;
    }
}
