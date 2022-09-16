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
                'group' => 'file',
                'name' => 'logo',
                'label' => 'Logo',
                'value' => null,
                'is_upload' => true,
                'show_form' => false,
                'active' => true,
            ],
            [
                'group' => 'file',
                'name' => 'logo_2',
                'label' => 'Logo 2',
                'value' => null,
                'is_upload' => true,
                'show_form' => false,
                'active' => true,
            ],
            [
                'group' => 'file',
                'name' => 'logo_small',
                'label' => 'Logo Small',
                'value' => null,
                'is_upload' => true,
                'show_form' => false,
                'active' => true,
            ],
            [
                'group' => 'file',
                'name' => 'logo_small_2',
                'label' => 'Logo Small 2',
                'value' => null,
                'is_upload' => true,
                'show_form' => false,
                'active' => true,
            ],
            [
                'group' => 'file',
                'name' => 'logo_mail',
                'label' => 'Logo Mail',
                'value' => null,
                'is_upload' => true,
                'show_form' => false,
                'active' => true,
            ],
            [
                'group' => 'file',
                'name' => 'open_graph',
                'label' => 'Open Graph',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 'file',
                'name' => 'banner_default',
                'label' => 'Banner',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],
            [
                'group' => 'file',
                'name' => 'cover_default',
                'label' => 'Cover Default',
                'value' => null,
                'is_upload' => true,
                'show_form' => true,
                'active' => true,
            ],

            // Group 2
            [
                'group' => 'general',
                'name' => 'website_name',
                'label' => 'Website Name',
                'value' => '4 Vision Media',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'website_description',
                'label' => 'Website Description',
                'value' => 'Perusahaan Jasa Pembuatan Website Software Aplikasi Desain Video & Konsultan IT',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            // [
            //     'group' => 'general',
            //     'name' => 'banner_limit',
            //     'label' => 'Banner Limit',
            //     'value' => 3,
            //     'is_upload' => false,
            //     'show_form' => true,
            //     'active' => false
            // ],
            [
                'group' => 'general',
                'name' => 'content_limit',
                'label' => 'Content Limit',
                'value' => 9,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'address',
                'label' => 'Address',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'email',
                'label' => 'Email',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'email_2',
                'label' => 'Email 2',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'fax',
                'label' => 'FAX',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'phone',
                'label' => 'Phone',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'phone_2',
                'label' => 'Phone 2',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'general',
                'name' => 'phone_whatsapp',
                'label' => 'Phone / URL Whatsapp',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],

            // Group 3
            [
                'group' => 'seo',
                'name' => 'meta_title',
                'label' => 'Meta Title',
                'value' => '4 Vision Media',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'seo',
                'name' => 'meta_description',
                'label' => 'Meta Description',
                'value' => 'Perusahaan Jasa Pembuatan Website Software Aplikasi Desain Video & Konsultan IT',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'seo',
                'name' => 'meta_keywords',
                'label' => 'Meta Keywords',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'seo',
                'name' => 'google_analytics',
                'label' => 'Google Analytics (script)',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'seo',
                'name' => 'google_verification',
                'label' => 'Google Verification',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'seo',
                'name' => 'domain_verification',
                'label' => 'Domain Verification',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],

            // Group 4
            [
                'group' => 'socmed',
                'name' => 'app_store',
                'label' => 'App Store',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'google_play_store',
                'label' => 'Google Play Store',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'facebook',
                'label' => 'Facebook URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'instagram',
                'label' => 'Instagram URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'linkedin',
                'label' => 'LinkedIn URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'pinterest',
                'label' => 'Pinterest URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'twitter',
                'label' => 'Twitter URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'whatsapp',
                'label' => 'WhatsApp URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'youtube',
                'label' => 'Youtube URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'youtube_id',
                'label' => 'Youtube ID',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'socmed',
                'name' => 'website',
                'label' => 'Website URL',
                'value' => null,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],

            // Notification
            [
                'group' => 'notif',
                'name' => 'notif_email_register',
                'label' => 'Register (Email)',
                'value' => true,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'notif',
                'name' => 'notif_email_inquiry',
                'label' => 'Inquiry (Email)',
                'value' => true,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'notif',
                'name' => 'notif_email_event',
                'label' => 'Event (Email)',
                'value' => true,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'notif',
                'name' => 'notif_apps_register',
                'label' => 'Register (App)',
                'value' => true,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'notif',
                'name' => 'notif_apps_inquiry',
                'label' => 'Inquiry (App)',
                'value' => true,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'notif',
                'name' => 'notif_apps_event',
                'label' => 'Event (App)',
                'value' => true,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            
            // Only Developer
            [
                'group' => 'dev',
                'name' => 'maintenance',
                'label' => 'Set web on maintenance ?',
                'value' => false,
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'dev',
                'name' => 'default_lang',
                'label' => 'Default Language',
                'value' => 'id',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'dev',
                'name' => 'system_email',
                'label' => 'System Email',
                'value' => 'developer@4visionmedia.com',
                'is_upload' => false,
                'show_form' => true,
                'active' => true
            ],
            [
                'group' => 'dev',
                'name' => 'pwa',
                'label' => 'Progressive Web App',
                'value' => false,
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
                'locked' => true
            ]);
        }
    }
}
