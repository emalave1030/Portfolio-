<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * The site's content, verbatim. `php artisan migrate:fresh --seed` reproduces
 * the live site exactly.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        Profile::query()->delete();
        Experience::query()->delete();
        Project::query()->delete();
        Skill::query()->delete();

        $this->seedProfile();
        $this->seedExperiences();
        $this->seedSkills();
        $this->seedProjects();
    }

    private function seedProfile(): void
    {
        Profile::create([
            'name' => 'Erwin Malave',
            'title' => 'Full Stack Software Engineer',
            'location' => 'Orlando, FL',
            'email' => 'contactme@erwinmalave.com',
            'site' => 'erwinmalave.com',
            'github_url' => 'https://github.com/emalave1030',
            'linkedin_url' => 'https://www.linkedin.com/in/erwin-malave-89a20291',
            'languages' => 'Bilingual English/Spanish',
            'about' => [
                'Full stack engineer with 5+ years designing and building web software that businesses run on every day. I build multi-tenant architectures, RESTful APIs, and role-based access control systems — currently powering parking for 500+ apartment and residential properties at ParkingPass.',
                'I work across Laravel, Vue 3, Angular, and React, with hands-on geospatial data visualization using Mapbox GL JS and QGIS. I own systems end to end — database design through UI — and I\'ve built in Web3, writing Solidity smart contracts for peer-to-peer Ethereum transfers.',
            ],
            'stats' => [
                ['value' => '5+', 'label' => 'years building software'],
                ['value' => '500+', 'label' => 'residential properties served'],
                ['value' => 'Laravel / Vue 3 / Angular / Mapbox GL JS', 'label' => 'core stack'],
            ],
        ]);
    }

    private function seedExperiences(): void
    {
        $experiences = [
            [
                'role' => 'Full Stack Engineer',
                'company' => 'ParkingPass',
                'location' => 'Orlando, FL',
                'start_date' => '2022-08-01',
                'end_date' => null,
                'bullets' => [
                    'Engineered RESTful APIs using Laravel across a multi-tenant SaaS platform serving 500+ residential communities — covering permit management, reporting, and authentication',
                    'Designed and implemented RBAC with multi-tiered permissions, enforcing secure, scoped data access across diverse property management clients',
                    'Architected a full rebuild of the registration wizard — centralizing previously standalone settings pages into a single unified workflow and shipping new features end-to-end',
                    'Integrated Mapbox GL JS for interactive geospatial parking zone features; authored documentation and walkthroughs enabling the team to build and extend map functionality independently',
                    'Built Vue 3 components with the Composition API, script setup, and Tailwind CSS delivering responsive interfaces for end users and administrators',
                    'Drove UX decisions through mockups and workflow design, translating product requirements into user-centered interfaces',
                    'Conducted backend and frontend testing including API response validation and UI verification, resolving production bugs to maintain platform reliability',
                    'Led client demo meetings, produced internal training videos for new features, and maintained developer documentation to support team-wide knowledge transfer',
                    'Modernized legacy codebases by rebuilding outdated interfaces into clean, modern implementations while simultaneously shipping new features',
                ],
            ],
            [
                'role' => 'Full Stack Developer',
                'company' => 'Elite EV',
                'location' => 'Orlando, FL',
                'start_date' => '2022-03-01',
                'end_date' => '2022-12-01',
                'bullets' => [
                    'Designed and developed a customer-facing website enabling users to browse EV products, locate nearby dealers, and find service locations',
                    'Migrated company email infrastructure to a new system with zero data loss',
                    'Managed WooCommerce sales and implemented SEO improvements to increase organic traffic and customer engagement',
                ],
            ],
            [
                'role' => 'Software Engineer / Full Stack & Web3 Developer',
                'company' => 'Trnde LLC',
                'location' => 'Orlando, FL',
                'start_date' => '2020-02-01',
                'end_date' => '2022-09-01',
                'bullets' => [
                    'Designed and built the frontend and backend for trndecoin.com, enabling users to send Ethereum to one another over the blockchain',
                    'Automated Solidity smart contracts so users can send cryptocurrency to each other securely and without a trusted intermediary',
                    'Designed the frontend and backend database for trnde-videos-final.vercel.app, a social platform where customers share videos and interact',
                    'Keep current with new languages and technologies so the projects stay maintainable for years to come',
                ],
            ],
        ];

        foreach ($experiences as $index => $experience) {
            Experience::create([...$experience, 'sort_order' => $index + 1]);
        }
    }

    private function seedSkills(): void
    {
        $groups = [
            'Languages' => ['JavaScript (ES6+)', 'TypeScript', 'PHP 8.4', 'HTML5', 'CSS3', 'C++', 'Solidity'],
            'Frontend' => ['Vue 3 (Composition API)', 'Vue Router', 'Angular', 'React', 'Vite', 'Tailwind CSS', 'CSS Modules', 'IndexedDB (offline-first storage)', 'Responsive design with light and dark themes'],
            'Backend' => ['Laravel 13', 'Node.js', 'REST APIs', 'GraphQL', 'Laravel Scheduler and Queues', 'Laravel Notifications and Mail', 'SQLite', 'Database Design', 'Multi-tenant architecture', 'RBAC'],
            'Geospatial' => ['Mapbox GL JS', 'QGIS', 'Map rendering', 'Geospatial data visualization'],
            'Security' => ['Web Crypto API (client-side encryption)', 'End-to-end encryption', 'Zero-knowledge authentication', 'Argon2id password hashing', 'CSP and security headers', 'CSRF protection and rate limiting', 'Signed URLs'],
            'Testing' => ['Pest', 'Puppeteer (headless-Chrome end-to-end tests)'],
            'DevOps & Workflow' => ['Laravel Forge', 'DigitalOcean', 'Zero-downtime push-to-deploy', 'Nginx', "Let's Encrypt SSL", 'DNS configuration', 'Git', 'GitHub', 'Jenkins', 'CI/CD', 'WordPress', 'Visual Studio Code', 'Agile workflows'],
            'Web3' => ['Solidity', 'Smart Contracts', 'Ethereum', 'Cryptocurrency'],
        ];

        // Geospatial is the differentiator and renders highlighted.
        $highlighted = 'Geospatial';

        $sortOrder = 0;

        foreach ($groups as $group => $names) {
            foreach ($names as $name) {
                Skill::create([
                    'name' => $name,
                    'group' => $group,
                    'is_highlighted' => $group === $highlighted,
                    'sort_order' => ++$sortOrder,
                ]);
            }
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            [
                'title' => 'NorthStar: Goal & Habit Tracker',
                'stack' => ['Laravel', 'Vue 3', 'TypeScript', 'Web Crypto', 'SQLite'],
                'link' => 'https://northstarplan.app',
                'description' => 'A privacy-first goal and habit tracker. Everything users write is encrypted in the browser, and zero-knowledge login means the password never leaves the device. It works offline, syncs when back online, and sends timezone-aware reminders without reading any user content.',
            ],
            [
                'title' => 'TrndeCoin',
                'stack' => ['Solidity', 'Ethereum', 'Smart Contracts', 'Full Stack'],
                'link' => 'https://trndecoin.com',
                'description' => 'Architected the frontend and backend for trndecoin.com, enabling peer-to-peer Ethereum transactions on the blockchain, with automated Solidity smart contracts handling secure, trustless transfers.',
            ],
            [
                'title' => 'Trnde Video Platform',
                'stack' => ['Full Stack', 'Vercel'],
                'link' => 'https://trnde-videos-final.vercel.app',
                'description' => 'A full-stack social video platform with user interaction and video sharing features, designed and implemented end to end.',
            ],
        ];

        foreach ($projects as $index => $project) {
            Project::create([...$project, 'sort_order' => $index + 1]);
        }
    }
}
