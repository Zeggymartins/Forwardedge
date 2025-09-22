<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Service;
use App\Models\ServiceContent;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        // Create the main Cybersecurity service
        $service = Service::create([
            'title' => 'Cybersecurity Solutions',
            'slug' => 'cybersecurity',
            'thumbnail' => null, // leave null for now
            'brief_description' => 'Protect your business with enterprise-grade cybersecurity strategies, monitoring, and defense.',
        ]);

        // Add contents
        $contents = [
            [
                'type' => 'heading',
                'content' => 'Comprehensive Cybersecurity for Modern Businesses',
                'position' => 1,
            ],
            [
                'type' => 'paragraph',
                'content' => 'In today’s digital-first world, cyber threats are evolving faster than ever. Our cybersecurity solutions safeguard your organization against data breaches, ransomware, phishing, and advanced persistent threats.',
                'position' => 2,
            ],
            [
                'type' => 'paragraph',
                'content' => 'We provide a holistic approach that combines proactive monitoring, vulnerability assessments, and incident response to ensure your business remains resilient against attacks.',
                'position' => 3,
            ],
            [
                'type' => 'list',
                'content' => json_encode([
                    '24/7 Security Monitoring',
                    'Threat Intelligence & Analysis',
                    'Penetration Testing & Vulnerability Scanning',
                    'Data Encryption & Protection',
                    'Cloud Security & Compliance',
                    'Disaster Recovery & Incident Response',
                ]),
                'position' => 4,
            ],
            [
                'type' => 'feature',
                'content' => json_encode([
                    'number' => '01',
                    'title' => 'Proactive Threat Detection',
                    'description' => 'We identify and neutralize threats before they can impact your business operations.',
                ]),
                'position' => 5,
            ],
            [
                'type' => 'feature',
                'content' => json_encode([
                    'number' => '02',
                    'title' => 'Regulatory Compliance',
                    'description' => 'Our team ensures your business meets global security standards such as ISO 27001, GDPR, and HIPAA.',
                ]),
                'position' => 6,
            ],
            [
                'type' => 'feature',
                'content' => json_encode([
                    'number' => '03',
                    'title' => 'Rapid Incident Response',
                    'description' => 'Minimize downtime and recover quickly with our dedicated response teams.',
                ]),
                'position' => 7,
            ],
        ];

        foreach ($contents as $content) {
            ServiceContent::create([
                'service_id' => $service->id,
                'type' => $content['type'],
                'content' => $content['content'],
                'position' => $content['position'],
            ]);
        }
    }
}