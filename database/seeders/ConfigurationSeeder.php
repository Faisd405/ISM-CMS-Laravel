<?php

namespace Database\Seeders;

use App\Models\Feature\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configurations = [
            // Group 1
            [
                'group' => 1,
                'name' => 'logo',
                'label' => 'Logo',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 1,
                'name' => 'logo_2',
                'label' => 'Logo 2',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 1,
                'name' => 'logo_small',
                'label' => 'Logo Small',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 1,
                'name' => 'logo_small_2',
                'label' => 'Logo Small 2',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 1,
                'name' => 'logo_mail',
                'label' => 'Logo Mail',
                'value' => null,
                'is_upload' => true,
                'show_form' => false,
                'active' => false,
            ],
            [
                'group' => 1,
                'name' => 'open_graph',
                'label' => 'Open Graph',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 1,
                'name' => 'banner_default',
                'label' => 'Banner',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 1,
                'name' => 'cover_default',
                'label' => 'Cover Default',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],

            // Group 2
            [
                'group' => 2,
                'name' => 'website_name',
                'label' => 'Website Name',
                'value' => '4 Vision Media',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'website_description',
                'label' => 'Website Description',
                'value' => 'Perusahaan Jasa Pembuatan Website Software Aplikasi Desain Video & Konsultan IT',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'banner_limit',
                'label' => 'Banner Limit',
                'value' => 3,
                'is_upload' => false,
                'show_form' => true,
                'active' => false
            ],
            [
                'group' => 2,
                'name' => 'content_limit',
                'label' => 'Content Limit',
                'value' => 9,
                'is_upload' => false,
                'show_form' => true,
                'active' => false
            ],
            [
                'group' => 2,
                'name' => 'address',
                'label' => 'Address',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'email',
                'label' => 'Email',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'email_2',
                'label' => 'Email 2',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'fax',
                'label' => 'FAX',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'phone',
                'label' => 'Phone',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'phone_2',
                'label' => 'Phone 2',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 2,
                'name' => 'phone_whatsapp',
                'label' => 'Phone / URL Whatsapp',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],

            // Group 3
            [
                'group' => 3,
                'name' => 'meta_title',
                'label' => 'Meta Title',
                'value' => '4 Vision Media',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 3,
                'name' => 'meta_description',
                'label' => 'Meta Description',
                'value' => 'Perusahaan Jasa Pembuatan Website Software Aplikasi Desain Video & Konsultan IT',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 3,
                'name' => 'meta_keywords',
                'label' => 'Meta Keywords',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 3,
                'name' => 'google_analytics',
                'label' => 'Google Analytics (script)',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 3,
                'name' => 'google_verification',
                'label' => 'Google Verification',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 3,
                'name' => 'domain_verification',
                'label' => 'Domain Verification',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],

            // Group 4
            [
                'group' => 4,
                'name' => 'app_store',
                'label' => 'App Store',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'google_play_store',
                'label' => 'Google Play Store',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'facebook',
                'label' => 'Facebook URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'instagram',
                'label' => 'Instagram URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'linkedin',
                'label' => 'LinkedIn URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'pinterest',
                'label' => 'Pinterest URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'twitter',
                'label' => 'Twitter URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'whatsapp',
                'label' => 'WhatsApp URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'youtube',
                'label' => 'Youtube URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'youtube_id',
                'label' => 'Youtube ID',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 4,
                'name' => 'website',
                'label' => 'Website URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            // Only Developer
            [
                'group' => 100,
                'name' => 'maintenance',
                'label' => 'Set web on maintenance ?',
                'value' => false,
                'is_upload' => false,
                'show_form' => true,
                'active' => false
            ],
            [
                'group' => 100,
                'name' => 'default_lang',
                'label' => 'Default Language',
                'value' => 'id',
                'is_upload' => false,
                'show_form' => true,
                'active' => false
            ],
            [
                'group' => 100,
                'name' => 'system_email',
                'label' => 'System Email',
                'value' => 'developer@4visionmedia.com',
                'is_upload' => false,
                'show_form' => true,
                'active' => false
            ],
            [
                'group' => 100,
                'name' => 'pwa',
                'label' => 'Progressive Web App',
                'value' => true,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ]
        ];

        foreach ($configurations as $item) {
            Configuration::create([
                'group' => $item['group'],
                'name' => $item['name'],
                'label' => $item['label'],
                'value' => $item['value'],
                'is_upload' => $item['is_upload'],
                'show_form' => $item['show_form'],
                'active' => $item['active'],
            ]);
        }
    }
}
