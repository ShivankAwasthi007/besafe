<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Google Maps
        DB::table('settings')->insert([
            [
                'name' => 'Google maps API key',
                'setting_type_id' => 1
            ],
        ]); 
        DB::table('settings')->insert([
            [
                'name' => 'Mapbox default public token',
                'setting_type_id' => 1
            ],
        ]); 
        // Personalize your system
        DB::table('settings')->insert([
            [
                'name' => 'System name',
                'value' => 'SBurK',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Company title',
                'value' => 'Company',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Company website',
                'value' => 'https://company.com',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Company email',
                'value' => 'contact_email@email.com',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Company telephone',
                'value' => '123456789 - 987654321',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Facebook link',
                'value' => 'https://www.facebook.com/company',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Twitter link',
                'value' => 'https://twitter.com/company',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Instagram link',
                'value' => 'https://instagram.com/company',
                'setting_type_id' => 2
            ],
            [
                'name' => 'Linkedin link',
                'value' => 'https://linkedin.com/company',
                'setting_type_id' => 2
            ],
        ]);
        // Currency and billing cycle
        DB::table('settings')->insert([
            [
                'name' => 'Currency',
                'value' => 'USD',
                'value_type' => 'select',
                'setting_type_id' => 3
            ],
            [
                'name' => 'Billing cycle',
                'value' => 'year',
                'value_type' => 'select',
                'setting_type_id' => 3
            ],
        ]);
        // SMS
        DB::table('settings')->insert([
            [
                'name' => 'SMS Gateway',
                'value' => 'none', //twilio, textlocal, branded sms, infobip
                'value_type' => 'select',
                'setting_type_id' => 4
            ],
        ]);
        // Twilio
        DB::table('settings')->insert([
            [
                'name' => 'Twilio account SID',
                'setting_type_id' => 4
            ],
            [
                'name' => 'Twilio auth token',
                'setting_type_id' => 4
            ],
            [
                'name' => 'Twilio phone number',
                'setting_type_id' => 4
            ],
        ]);
        // Textlocal
        DB::table('settings')->insert([
            [
                'name' => 'Textlocal Apikey',
                'setting_type_id' => 4
            ],
            [
                'name' => 'Textlocal Sender',
                'setting_type_id' => 4
            ],
        ]);
        // Branded SMS (smstoconnect)
        DB::table('settings')->insert([
            [
                'name' => 'Branded SMS Apikey',
                'setting_type_id' => 4
            ],
            [
                'name' => 'Branded SMS Sender',
                'setting_type_id' => 4
            ],
        ]);
        // Infobip
        DB::table('settings')->insert([
            [
                'name' => 'Infobip Apikey',
                'setting_type_id' => 4
            ],
            [
                'name' => 'Infobip BaseUrl',
                'setting_type_id' => 4
            ],
        ]);

        // Payment
        DB::table('settings')->insert([
            [
                'name' => 'Payment Gateway',
                'value' => 'none', //Paypal, Stripe, Razorpay
                'value_type' => 'select',
                'setting_type_id' => 5
            ],
        ]);
        // Paypal
        // DB::table('settings')->insert([
        //     [
        //         'name' => 'Paypal Client ID',
        //         'setting_type_id' => 5
        //     ],
        //     [
        //         'name' => 'Paypal Secret',
        //         'setting_type_id' => 5
        //     ],
        // ]);
        // DB::table('settings')->insert([
        //     [
        //         'name' => 'Paypal Environment',
        //         'value' => 'sandbox', //live
        //         'value_type' => 'select',
        //         'setting_type_id' => 5
        //     ],
        // ]);

        // Stripe
        DB::table('settings')->insert([
            [
                'name' => 'Stripe Publishable key',
                'setting_type_id' => 5
            ],
            [
                'name' => 'Stripe Secret key',
                'setting_type_id' => 5
            ],
        ]);

        // Razorpay
        DB::table('settings')->insert([
            [
                'name' => 'Razorpay Key Id',
                'setting_type_id' => 5
            ],
            [
                'name' => 'Razorpay Key Secret',
                'setting_type_id' => 5
            ],
        ]);
        // Flutterwave
        DB::table('settings')->insert([
            [
                'name' => 'Flutterwave Public Key',
                'setting_type_id' => 5
            ],
            [
                'name' => 'Flutterwave Secret Key',
                'setting_type_id' => 5
            ],
        ]);
        DB::table('settings')->insert([
            [
                'name' => 'Flutterwave Environment',
                'value' => 'staging', //live
                'value_type' => 'select',
                'setting_type_id' => 5
            ],
        ]);
        // Paytabs
        DB::table('settings')->insert([
            [
                'name' => 'Paytabs API Server Key',
                'setting_type_id' => 5
            ],
            [
                'name' => 'Paytabs Profile Id',
                'setting_type_id' => 5
            ],
        ]);
        DB::table('settings')->insert([
            [
                'name' => 'Paytabs Region',
                'value' => 'EGY',
                'value_type' => 'select',
                'setting_type_id' => 5
            ],
        ]);
    }
}
