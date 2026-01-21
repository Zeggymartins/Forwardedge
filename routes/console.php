<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;

/*
|------------------------------------------------------------------------------
| Page Builder Installer (Laravel 11/12 style, no Console Kernel needed)
|------------------------------------------------------------------------------
| Usage:
|   php artisan pb:install           # scaffold files (won’t overwrite)
|   php artisan pb:install --force   # scaffold and overwrite if exists
|   php artisan pb:install --seed    # also drop a sample Bootcamp seeder file
|
| After --seed, run:
|   php artisan db:seed --class=BootcampPageSeeder
|
*/


Artisan::command('pb:install {--force} {--seed}', function () {
    $force = (bool) $this->option('force');
    $seed  = (bool) $this->option('seed');

    // capture the Command instance via a normal variable, NOT $this
    $cmd = $this;

    $write = function (string $path, string $contents) use ($force, $cmd) {
        $exists = File::exists($path);
        if ($exists && !$force) {
            $cmd->comment("• Skipped (exists): " . str_replace(base_path().DIRECTORY_SEPARATOR, '', $path));
            return false;
        }
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);
        $cmd->line('• ' . str_replace(base_path().DIRECTORY_SEPARATOR, '', $path));
        return true;
    };

    // 1) config/pagebuilder.php
    $write(config_path('pagebuilder.php'), <<<'PHP'
<?php

return [
    'globals' => [
        'header_block_type' => null,
        'footer_block_type' => null,
    ],

    'kinds' => [
        'generic' => [
            'label' => 'Generic Page',
            'required' => [],
            'one_of_groups' => [],
            'allowed' => null,
        ],

        'bootcamp' => [
            'label' => 'Bootcamp Page',
            'required' => ['hero','program_overview','pricing_recap','faq','closing_cta'],
            'one_of_groups' => [
                ['foundations', 'curriculum'],
            ],
            'allowed' => null,
        ],
    ],

    'block_rules' => [
        'hero' => [
            'title' => ['required','string','min:3'],
        ],
        'program_overview' => [
            'items' => ['required','array','min:1'],
            'items.*.title' => ['nullable','string'],
            'items.*.text'  => ['nullable','string'],
        ],
        'foundations' => [
            'bullets' => ['required','array','min:3'],
        ],
        'curriculum' => [
            'phases' => ['required','array','min:1'],
        ],
        'pricing_recap' => [
            'plans' => ['required','array','min:1'],
            'plans.*.name' => ['required','string'],
            'plans.*.price'=> ['required','string'],
        ],
        'faq' => [
            'items' => ['required','array','min:1'],
            'items.*.q' => ['required','string'],
            'items.*.a' => ['required','string'],
        ],
        'closing_cta' => [
            'title' => ['required','string','min:3'],
        ],
    ],
];
PHP);

    // 2) app/Support/PageBlueprint.php
    $write(app_path('Support/PageBlueprint.php'), <<<'PHP'
<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Support\Facades\Validator;

class PageBlueprint
{
    public static function for(string $kind): array
    {
        $cfg = config('pagebuilder.kinds.' . $kind);
        return $cfg ?: config('pagebuilder.kinds.generic');
    }

    public static function requiredBlocks(string $kind): array
    {
        return static::for($kind)['required'] ?? [];
    }

    public static function oneOfGroups(string $kind): array
    {
        return static::for($kind)['one_of_groups'] ?? [];
    }

    public static function blockRules(string $type): array
    {
        return config('pagebuilder.block_rules.' . $type, []);
    }

    /**
     * Validate a page against its blueprint.
     * Returns ['ok'=>bool, 'errors'=>array]
     */
    public static function validatePage(Page $page): array
    {
        $errors = [];
        $kind = $page->page_kind ?? 'generic';
        $required = static::requiredBlocks($kind);
        $oneOfGroups = static::oneOfGroups($kind);

        $blocks = $page->blocks()->where('is_published', true)->with('children')->get();
        $types  = $blocks->pluck('type')->all();

        foreach ($required as $type) {
            if (!in_array($type, $types, true)) {
                $errors[] = "Required block missing: {$type}";
            }
        }

        foreach ($oneOfGroups as $group) {
            $ok = false;
            foreach ($group as $t) {
                if (in_array($t, $types, true)) { $ok = true; break; }
            }
            if (!$ok) {
                $errors[] = "At least one of the following blocks is required: " . implode(', ', $group);
            }
        }

        foreach ($blocks as $b) {
            $rules = static::blockRules($b->type);
            if (!empty($rules)) {
                $v = Validator::make($b->data ?? [], $rules);
                if ($v->fails()) {
                    foreach ($v->errors()->all() as $msg) {
                        $errors[] = "[{$b->type}] {$msg}";
                    }
                }
            }
        }

        return ['ok' => empty($errors), 'errors' => $errors];
    }
}
PHP);

    // 3) resources/views/user/pages/dynamic.blade.php
    $write(resource_path('views/user/pages/dynamic.blade.php'), <<<'BLADE'
@extends('user.master_page')

@section('title', ($page->title ?? 'Page') . ' | Forward Edge Consulting')

@section('main')
  <main id="primary" class="site-main">

    {{-- Optional global header/footer from config --}}
    @php $headerType = config('pagebuilder.globals.header_block_type'); @endphp
    @if($headerType) @includeIf('user.pages.block.' . $headerType, ['block' => (object)['data'=>[]]]) @endif

    @foreach($page->blocks as $block)
      @includeIf('user.pages.block.' . $block->type, ['block' => $block])
    @endforeach

    @php $footerType = config('pagebuilder.globals.footer_block_type'); @endphp
    @if($footerType) @includeIf('user.pages.block.' . $footerType, ['block' => (object)['data'=>[]]]) @endif

  </main>
@endsection
BLADE);

    // 4) Block stubs under resources/views/user/pages/block
    $blockDir = resource_path('views/user/pages/block');
    File::ensureDirectoryExists($blockDir);

    $blocks = [
        'hero.blade.php' => <<<'BLADE'
@php $d = $block->data ?? []; @endphp
<section class="tj-banner-section h6-hero section-gap-x">
  <div class="banner-area">
    <div class="banner-left-box">
      <div class="banner-content">
        <h1 class="banner-title title-anim">{{ $d['title'] ?? 'Bootcamp' }}</h1>
        @if(!empty($d['subtitle']))
          <p class="desc wow fadeInUp" data-wow-delay=".5s">{{ $d['subtitle'] }}</p>
        @endif
        <div class="btn-area wow fadeInUp" data-wow-delay=".8s">
          @if(!empty($d['cta_primary']))
            <a class="tj-primary-btn tj-primary-btn-lg" href="{{ $d['cta_primary']['url'] ?? '#' }}">
              <span class="btn-text"><span>{{ $d['cta_primary']['label'] ?? 'Get Started' }}</span></span>
              <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
            </a>
          @endif
          @if(!empty($d['cta_secondary']))
            <a class="tj-primary-btn transparent-btn ms-3" href="{{ $d['cta_secondary']['url'] ?? '#' }}">
              <span class="btn-text"><span>{{ $d['cta_secondary']['label'] ?? 'Learn More' }}</span></span>
              <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
            </a>
          @endif
        </div>
      </div>
    </div>
    @if(!empty($d['image']))
      <div class="banner-right-box">
        <img class="image-box" src="{{ $d['image'] }}" alt="Hero">
      </div>
    @endif
  </div>
</section>
BLADE,
        'program_overview.blade.php' => <<<'BLADE'
@php $d = $block->data ?? []; $items = $d['items'] ?? []; @endphp
<section class="section-gap">
  <div class="container">
    <div class="sec-heading-wrap">
      <div class="heading-wrap-content">
        <div class="sec-heading">
          <h2 class="sec-title title-anim">{{ $d['title'] ?? 'Program Overview' }}</h2>
        </div>
        @if(!empty($d['subtitle'])) <p class="desc">{{ $d['subtitle'] }}</p> @endif
      </div>
    </div>
    <div class="row g-4 mt-3">
      @foreach($items as $it)
        <div class="col-md-6 col-lg-4">
          <div class="image-box p-4 h-100">
            <h5 class="mb-2">{{ $it['title'] ?? '' }}</h5>
            <p class="mb-0 small">{{ $it['text'] ?? '' }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
BLADE,
        'foundations.blade.php' => <<<'BLADE'
@php $d = $block->data ?? []; $bullets = $d['bullets'] ?? []; $outcomes = $d['outcomes'] ?? []; @endphp
<section class="section-gap">
  <div class="container">
    <h3 class="mb-2">{{ $d['title'] ?? 'Foundations' }}</h3>
    @if(!empty($d['subtitle'])) <p class="text-muted mb-3">{{ $d['subtitle'] }}</p> @endif
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="image-box p-4 h-100">
          <h6 class="mb-2">Key Learning Areas</h6>
          <ul class="mb-0">@foreach($bullets as $b)<li>{{ $b }}</li>@endforeach</ul>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="image-box p-4 h-100">
          <h6 class="mb-2">Outcomes</h6>
          <ul class="mb-3">@foreach($outcomes as $o)<li>{{ $o }}</li>@endforeach</ul>
          @if(!empty($d['pricing'])) <div class="h5 fw-bold mb-3">{{ $d['pricing'] }}</div> @endif
          <div class="d-flex gap-2 flex-wrap">
            @if(!empty($d['cta_enroll']))
              <a class="tj-primary-btn" href="{{ $d['cta_enroll']['url'] ?? '#' }}">
                <span class="btn-text"><span>{{ $d['cta_enroll']['label'] ?? 'Enroll' }}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            @endif
            @if(!empty($d['cta_scholarship']))
              <a class="tj-primary-btn transparent-btn" href="{{ $d['cta_scholarship']['url'] ?? '#' }}">
                <span class="btn-text"><span>{{ $d['cta_scholarship']['label'] ?? 'Apply for Scholarship' }}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
BLADE,
        'curriculum.blade.php' => <<<'BLADE'
@php $phases = $block->data['phases'] ?? []; @endphp
<section class="section-gap">
  <div class="container">
    <h3 class="mb-4">{{ $block->data['title'] ?? 'Curriculum' }}</h3>
    <div class="row g-3">
      @foreach($phases as $phase)
        <div class="col-md-6">
          <div class="image-box p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0">{{ $phase['title'] ?? '' }}</h5>
              @if(!empty($phase['weeks'])) <span class="badge bg-dark-subtle">{{ $phase['weeks'] }} weeks</span> @endif
            </div>
            <ul class="mb-0 small">
              @foreach(($phase['topics'] ?? []) as $topic)<li>{{ $topic }}</li>@endforeach
            </ul>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
BLADE,
        'pricing_recap.blade.php' => <<<'BLADE'
@php $plans = $block->data['plans'] ?? []; @endphp
<section class="section-gap">
  <div class="container">
    <h3 class="mb-4">{{ $block->data['title'] ?? 'Pricing' }}</h3>
    <div class="row g-4">
      @foreach($plans as $p)
      <div class="col-md-6 col-lg-3">
        <div class="image-box p-4 h-100">
          <h6 class="mb-1">{{ $p['name'] ?? '' }}</h6>
          <div class="fw-bold mb-2">{{ $p['price'] ?? '' }}</div>
          @if(!empty($p['notes'])) <p class="small text-muted">{{ $p['notes'] }}</p> @endif
          @if(!empty($p['cta']))
            <a class="text-btn" href="{{ $p['cta']['url'] ?? '#' }}">
              <span class="btn-text"><span>{{ $p['cta']['label'] ?? 'Enroll' }}</span></span>
              <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
            </a>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
BLADE,
        'faq.blade.php' => <<<'BLADE'
@php $faqs = $block->data['items'] ?? []; @endphp
<section class="section-gap">
  <div class="container">
    <h3 class="mb-4">{{ $block->data['title'] ?? 'FAQ' }}</h3>
    <div class="row g-3">
      @foreach($faqs as $q)
      <div class="col-12">
        <div class="image-box p-4">
          <strong class="d-block mb-1">{{ $q['q'] ?? '' }}</strong>
          <p class="mb-0">{{ $q['a'] ?? '' }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
BLADE,
        'closing_cta.blade.php' => <<<'BLADE'
@php $d = $block->data ?? []; @endphp
<section class="section-gap">
  <div class="container text-center">
    <h2 class="mb-2">{{ $d['title'] ?? 'Ready to begin?' }}</h2>
    @if(!empty($d['subtitle'])) <p class="text-muted mb-4">{{ $d['subtitle'] }}</p> @endif
    <div class="d-inline-flex flex-wrap gap-2 justify-content-center">
      @foreach(($d['ctas'] ?? []) as $c)
      <a class="tj-primary-btn" href="{{ $c['url'] ?? '#' }}">
        <span class="btn-text"><span>{{ $c['label'] ?? 'Learn more' }}</span></span>
        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
      </a>
      @endforeach
    </div>
  </div>
</section>
BLADE,
    ];

    foreach ($blocks as $file => $contents) {
        $write($blockDir . DIRECTORY_SEPARATOR . $file, $contents);
    }

    // 5) Optional seeder
    if ($seed) {
        $write(database_path('seeders/BootcampPageSeeder.php'), <<<'PHP'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\Block;

class BootcampPageSeeder extends Seeder
{
    public function run(): void
    {
        if (!class_exists(Page::class) || !class_exists(Block::class)) return;
        if (!\Schema::hasTable('pages') || !\Schema::hasTable('blocks')) return;

        $page = Page::updateOrCreate(
            ['slug' => 'cybersecurity-bootcamp'],
            [
                'title'     => 'Cybersecurity Bootcamp',
                'status'    => 'published',
                'template'  => 'user.pages.dynamic',
                'page_kind' => method_exists(Page::class, 'getFillable') && in_array('page_kind',(new Page)->getFillable(),true)
                    ? 'bootcamp' : 'generic',
                'meta'      => ['description'=>'Live foundations + optional specializations'],
            ]
        );

        $order = 0;
        $add = function(array $typeAndData) use ($page, &$order) {
            Block::create([
                'page_id' => $page->id,
                'type' => $typeAndData['type'],
                'order'=> $order += 10,
                'data' => $typeAndData['data'] ?? [],
                'is_published' => true,
            ]);
        };

        $add(['type'=>'hero','data'=>[
            'title'=>'Bootcamp 5.0: Live Foundations + Self-Paced Specializations',
            'subtitle'=>'Start your cybersecurity career and specialize at your pace.',
            'cta_primary'=>['label'=>'Enroll in Foundations','url'=>'/enroll/foundations'],
            'cta_secondary'=>['label'=>'Apply for Scholarship','url'=>'/scholarships/apply'],
        ]]);

        $add(['type'=>'program_overview','data'=>[
            'title'=>'Two Steps. One Career Pathway.',
            'items'=>[
                ['title'=>'Foundations (Live, 5 Weeks)','text'=>'15 live classes, labs, certificate'],
                ['title'=>'Specializations (Self-Paced)','text'=>'Pentesting, SOC, or GRC'],
                ['title'=>'Tools & Projects','text'=>'Real tools, guided labs'],
            ],
        ]]);

        $add(['type'=>'foundations','data'=>[
            'title'=>'Foundational Training (5 Weeks, Live)',
            'subtitle'=>'Your Launchpad',
            'bullets'=>['Cyber basics','Windows & Linux','Networking & Traffic','Crypto & Passwords'],
            'outcomes'=>['Hands-on labs','Beginner-friendly','Certificate'],
            'pricing'=>'Fee: ₦100,000 / $67 • Scholarship available',
            'cta_enroll'=>['label'=>'Enroll','url'=>'/enroll/foundations'],
            'cta_scholarship'=>['label'=>'Apply for Scholarship','url'=>'/scholarships/apply'],
        ]]);

        $add(['type'=>'pricing_recap','data'=>[
            'plans'=>[
                ['name'=>'Foundations','price'=>'₦100,000 / $67','cta'=>['label'=>'Enroll','url'=>'/enroll/foundations']],
                ['name'=>'Pentesting (Self-Paced)','price'=>'₦50,000 / $33','cta'=>['label'=>'Get Pentesting','url'=>'/enroll/pentest']],
                ['name'=>'SOC & IR (Self-Paced)','price'=>'₦50,000 / $33','cta'=>['label'=>'Get SOC','url'=>'/enroll/soc']],
                ['name'=>'GRC (Self-Paced)','price'=>'₦50,000 / $33','cta'=>['label'=>'Get GRC','url'=>'/enroll/grc']],
            ],
        ]]);

        $add(['type'=>'faq','data'=>[
            'items'=>[
                ['q'=>'Do I need prior experience?','a'=>'No—this is beginner friendly.'],
                ['q'=>'Are specializations required?','a'=>'Optional; pick what suits your path.'],
            ],
        ]]);

        $add(['type'=>'closing_cta','data'=>[
            'title'=>'Your cybersecurity career starts here.',
            'subtitle'=>'Join the next cohort.',
            'ctas'=>[
                ['label'=>'Enroll in Foundations','url'=>'/enroll/foundations'],
                ['label'=>'Apply for Scholarship','url'=>'/scholarships/apply'],
            ],
        ]]);
    }
}
PHP);
        $this->line('• Seeder published. Run: php artisan db:seed --class=BootcampPageSeeder');
    }

    $this->info('Page Builder scaffolding complete ✅');
    $this->comment('Ensure your controller renders: view($page->template ?? "user.pages.dynamic", compact("page"))');
})->purpose('Scaffold page-builder files (Laravel 11/12, no Console Kernel)');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/
Schedule::command('queue:retry-mail 10 --once --after="2026-01-19 00:00:00"')
    ->everyFiveMinutes()
    ->withoutOverlapping();
