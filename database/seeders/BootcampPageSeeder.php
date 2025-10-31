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