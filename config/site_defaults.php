<?php

/*
 * Default website settings. These are copied into the database during
 * installation; after that everything is edited from Admin → Site Settings.
 */
return [

    // Business
    'business_name' => 'SM Cab Service',
    'legal_name' => '',
    'logo_text' => 'SM [Cab Service]',
    'tagline' => '',
    'logo' => '',
    'show_name_with_logo' => false,
    'favicon' => 'images/general/favicon.svg',
    'apple_touch_icon' => '',
    'website_url' => '',

    // Local SEO (Google Business Profile: SM Cab Service - Taxi Service In Dehradun)
    'schema_type' => 'TaxiService',
    'schema_street' => 'Kanhaiya Vihar, Kargi',
    'city' => 'Dehradun',
    'state' => 'Uttarakhand',
    'pincode' => '248121',
    'schema_area_served' => 'Dehradun, Mussoorie, Rishikesh, Haridwar, Uttarakhand',
    'schema_opening_hours' => '',
    'price_range' => '₹₹',
    'default_og_image' => '',
    // add your own codes in Admin → Site Settings (Google Search Console / Bing)
    'google_site_verification' => '',
    'bing_site_verification' => '',

    // Contact
    'mobile' => '+91 89584 24758',
    'mobile_2' => '',
    'whatsapp_number' => '918958424758',
    'email' => '',
    'address' => 'Kanhaiya Vihar, Kargi, Dehradun, Uttarakhand 248121',
    'working_hours' => 'Call or WhatsApp to book',

    // WhatsApp messages
    'whatsapp_message' => 'Hi SM Cab Service, I want to book a cab.',
    'whatsapp_card_message' => 'Hi SM Cab Service, I want to book a {item}. Please share the fare.',

    // Colours (dark = navy, accent = taxi yellow, primary = deep blue, light = section grey)
    'colors' => [
        'primary' => '#1E3A8A',
        'dark' => '#0B1B33',
        'accent' => '#FFC21A',
        'light' => '#F6F8FB',
    ],

    // Buttons
    'buttons' => [
        'main_bg' => '#FFC21A',
        'main_color' => '#0B1B33',
        'call_text' => 'Call Now',
        'call_bg' => '#0B1B33',
        'call_color' => '#FFFFFF',
        'whatsapp_text' => 'WhatsApp',
        'whatsapp_bg' => '#1FAF54',
        'whatsapp_color' => '#FFFFFF',
        'submit_text' => 'Get Fare & Book Now',
        'submit_bg' => '#FFC21A',
        'submit_color' => '#0B1B33',
        'popup_text' => 'Get Fare & Book Now',
        'popup_bg' => '#FFC21A',
        'popup_color' => '#0B1B33',
        'sending_text' => 'Sending…',
        'more_text' => 'Book now',
        'card_book_text' => 'Book Now',
        'card_call_text' => 'Call',
        'card_whatsapp_text' => 'WhatsApp',
        'card_show_call' => false,
        'card_show_whatsapp' => true,
        'email_action_word' => 'Booking',
    ],

    // Master cab-card style (empty = design default)
    'card' => [
        'radius' => '',
        'badge_bg' => '',
        'badge_color' => '',
        'label_bg' => '',
        'label_color' => '',
        'icon_bg' => '',
        'price_label' => 'Starting',
        'request_text' => 'Get Quote',
        'request_note' => '',
        'filter_all_text' => 'All',
    ],

    // What happens after a form is sent
    'form_action' => [
        'send_email' => true,
        'after_submit' => 'thankyou',       // thankyou | whatsapp (thank-you page offers "Send booking details on WhatsApp")
        'thankyou_wa_share' => true,
        'share_text' => 'Send booking details on WhatsApp',
        'wa_number' => '',
        'wa_template' => "Hi {business}, I want to book a cab.\n\n{details}",
        'datalayer_data' => false,
    ],

    // Landing pages (own header + footer)
    'landing' => [
        'call_text' => 'Call Now',
        'call_show_number' => false,
        'call_desktop' => true,
        'call_mobile' => true,
        'enq_text' => 'Book Cab',
        'enq_desktop' => true,
        'enq_mobile' => true,
        'sticky' => true,
        'header_bg' => '',
        'header_color' => '',
        'footer_show' => true,
        'footer_text' => '© {year} {business}. All rights reserved.',
        'footer_note' => '',
        'footer_bg' => '',
        'footer_color' => '',
        'mobile_bar' => true,
        'mb_call' => true,
        'mb_wa' => true,
        'mb_enq' => true,
        'floating' => false,
    ],

    // Top bar + header
    'topbar_show' => true,
    'topbar_text' => 'Taxi Service in {city}, Uttarakhand',
    'topbar_email_show' => true,
    'topbar_hours_show' => false,
    'topbar_phone_show' => true,
    'topbar_phone_label' => '',
    // same menu as the old website
    'menu' => [
        ['label' => 'Home', 'link' => ''],
        ['label' => 'About Us', 'link' => 'about-us.php'],
        ['label' => 'Cab Services', 'link' => 'cab-services.php'],
        ['label' => 'Join Us', 'link' => 'join-us.php'],
        ['label' => 'Blogs', 'link' => 'blogs.php'],
        ['label' => 'Contact Us', 'link' => 'contact-us.php'],
    ],
    'home_menu' => [],
    'header_wa_show' => true,
    'header_wa_text' => 'WhatsApp',
    'header_phone_show' => true,
    'header_phone_label' => 'Call Now',
    'header_button_show' => false,
    'header_button_text' => 'Book Cab',
    'inner_hero_image' => '',

    // Booking popup
    'popup_small' => 'Get fare & confirmation on WhatsApp',
    'popup_heading' => 'Book Your Cab',
    'popup_item_suffix' => '',
    'popup_image' => '',

    // Yellow call-to-action band
    'cta_heading' => 'Need a cab right now?',
    'cta_text' => "Call or WhatsApp – we'll confirm your ride in minutes.",
    'cta_image' => '',
    'cta_call_text' => 'Call {phone}',
    'cta_wa_text' => 'WhatsApp',
    'cta_book_text' => '-',

    // 404 page
    'notfound_title' => 'Page Not Found',
    'notfound_text' => 'The page you are looking for does not exist.',
    'notfound_body' => 'Please use the menu above, or go back to the home page to book your cab.',
    'notfound_button' => 'Go to Home Page',

    // Footer
    'footer_about' => "Taxi & cab service in Dehradun – airport transfers, Mussoorie, Rishikesh & Haridwar trips, Char Dham yatra and outstation travel across Uttarakhand.",
    'footer_disclaimer' => '',
    'footer_quick_title' => 'Services',
    // one service page per link (database/seeders/data/service_pages.php)
    'footer_quick_links' => [
        ['label' => 'Dehradun Airport Taxi', 'link' => 'dehradun-airport-taxi.php'],
        ['label' => 'Dehradun to Mussoorie Taxi', 'link' => 'dehradun-to-mussoorie-taxi.php'],
        ['label' => 'Rishikesh & Haridwar Taxi', 'link' => 'dehradun-to-rishikesh-haridwar-taxi.php'],
        ['label' => 'Char Dham Yatra Taxi', 'link' => 'char-dham-yatra-taxi.php'],
        ['label' => 'Outstation Taxi', 'link' => 'outstation-taxi-dehradun.php'],
        ['label' => 'Local Taxi in Dehradun', 'link' => 'local-taxi-dehradun.php'],
    ],
    'footer_info_title' => 'Company',
    'footer_info_links' => [
        ['label' => 'About Us', 'link' => 'about-us.php'],
        ['label' => 'Join Us', 'link' => 'join-us.php'],
        ['label' => 'Blogs', 'link' => 'blogs.php'],
        ['label' => 'Contact Us', 'link' => 'contact-us.php'],
        ['label' => 'Privacy Policy', 'link' => 'privacy-policy.php'],
        ['label' => 'Terms & Conditions', 'link' => 'terms-and-conditions.php'],
        ['label' => 'Cancellation Policy', 'link' => 'cancellation-policy.php'],
    ],
    // "Popular searches" row at the bottom of the footer
    'footer_tags_title' => 'Popular Searches',
    'footer_tags' => [
        ['label' => 'Taxi Service in Dehradun', 'link' => ''],
        ['label' => 'Cab Service in Dehradun', 'link' => 'cab-services.php'],
        ['label' => 'Dehradun Airport Taxi', 'link' => 'dehradun-airport-taxi.php'],
        ['label' => 'Jolly Grant Airport Taxi', 'link' => 'dehradun-airport-taxi.php'],
        ['label' => 'Dehradun to Mussoorie Taxi', 'link' => 'dehradun-to-mussoorie-taxi.php'],
        ['label' => 'Dehradun to Rishikesh Taxi', 'link' => 'dehradun-to-rishikesh-haridwar-taxi.php'],
        ['label' => 'Dehradun to Haridwar Taxi', 'link' => 'dehradun-to-rishikesh-haridwar-taxi.php'],
        ['label' => 'Char Dham Yatra Taxi', 'link' => 'char-dham-yatra-taxi.php'],
        ['label' => 'Kedarnath Taxi from Dehradun', 'link' => 'char-dham-yatra-taxi.php'],
        ['label' => 'Dehradun to Nainital Taxi', 'link' => 'outstation-taxi-dehradun.php'],
        ['label' => 'Outstation Taxi Dehradun', 'link' => 'outstation-taxi-dehradun.php'],
        ['label' => 'Local Taxi in Dehradun', 'link' => 'local-taxi-dehradun.php'],
    ],
    'footer_contact_title' => 'Contact',
    'footer_hours_show' => false,
    'social' => [
        'facebook' => '',
        'instagram' => '',
        'youtube' => '',
        'linkedin' => '',
        'x' => '',
    ],
    'copyright' => '© {year} {business}. All rights reserved.',
    // credit for the Wikimedia car photos on the cab cards (their licence asks for it)
    'footer_credit_text' => 'Car photos for illustration · Photo credits',
    'footer_credit_link' => 'photo-credits.php',

    // Booking form fields (one list for all forms)
    'form_fields' => [
        ['name' => 'trip', 'label' => 'Trip type', 'type' => 'trip', 'required' => true, 'placeholder' => '', 'error' => 'Please choose a trip type', 'options' => "One Way\nRound Trip\nLocal\nAirport", 'min' => null, 'max' => null, 'short_form' => true],
        ['name' => 'pickup', 'label' => 'Pickup location', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Dehradun Railway Station, ISBT', 'error' => 'Please enter the pickup location', 'icon' => 'pin', 'options' => '', 'min' => 2, 'max' => 120, 'short_form' => true],
        ['name' => 'drop', 'label' => 'Drop location', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Mussoorie, Rishikesh, Haridwar', 'error' => 'Please enter the drop location', 'icon' => 'route', 'show_for' => 'One Way, Round Trip, Airport', 'options' => '', 'min' => 2, 'max' => 120, 'short_form' => true],
        ['name' => 'package', 'label' => 'Local package', 'type' => 'select', 'required' => true, 'placeholder' => 'Choose a package', 'error' => 'Please choose a package', 'show_for' => 'Local', 'options' => "4 Hours / 40 km\n8 Hours / 80 km\n12 Hours / 120 km", 'min' => null, 'max' => null, 'short_form' => true],
        ['name' => 'date', 'label' => 'Pickup date', 'type' => 'date', 'required' => true, 'placeholder' => '', 'error' => 'Please choose today or a future date', 'default' => 'today', 'half' => true, 'options' => '', 'min' => null, 'max' => null, 'short_form' => true],
        ['name' => 'time', 'label' => 'Time', 'type' => 'time', 'required' => true, 'placeholder' => '', 'error' => 'Please choose a time', 'default' => '09:00', 'half' => true, 'options' => '', 'min' => null, 'max' => null, 'short_form' => true],
        ['name' => 'return_date', 'label' => 'Return date', 'type' => 'date', 'required' => true, 'placeholder' => '', 'error' => 'Please choose the return date', 'show_for' => 'Round Trip', 'options' => '', 'min' => null, 'max' => null, 'short_form' => true],
        ['name' => 'car', 'label' => 'Select car', 'type' => 'select', 'required' => true, 'placeholder' => 'Choose your car', 'error' => 'Please choose a car', 'options' => 'services', 'min' => null, 'max' => null, 'short_form' => true],
        ['name' => 'name', 'label' => 'Your name', 'type' => 'text', 'required' => true, 'placeholder' => 'Name', 'error' => 'Please enter your name', 'half' => true, 'options' => '', 'min' => null, 'max' => null, 'short_form' => true],
        ['name' => 'mobile', 'label' => 'Mobile number', 'type' => 'tel', 'required' => true, 'placeholder' => '10-digit mobile', 'error' => 'Please enter a valid 10-digit mobile number', 'half' => true, 'options' => '', 'min' => null, 'max' => null, 'short_form' => true],
    ],
    'privacy_note' => 'No advance payment needed to enquire. We never share your number.',
    'form_error_text' => 'Please correct the highlighted fields.',
    'form_offline_text' => 'Could not send. Please check your internet connection, or call us directly.',

    // Email / SMTP
    'mail_to' => '',
    'mail_cc' => '',
    'smtp_on' => true,
    'smtp_host' => 'smtp.hostinger.com',
    'smtp_port' => 465,
    'smtp_secure' => 'ssl',
    'smtp_username' => '',
    'smtp_password' => '',
    'from_email' => '',
    'from_name' => 'SM Cab Service Website',
    'autoreply_on' => false,
    'autoreply_subject' => 'Thank you for booking with SM Cab Service',
    'autoreply_message' => "Dear {name},\n\nThank you for your booking request for {item}. Our team will call you shortly to confirm the fare and pickup time.\n\nFor urgent help please call {phone}.\n\nRegards,\nSM Cab Service",

    // Thank-you page buttons
    'thankyou' => [
        'call_text' => 'Call Now',
        'call_bg' => '#FFC21A',
        'call_color' => '#0B1B33',
        'wa_message' => 'Hi SM Cab Service, I just sent a booking request.',
        'home_text' => 'Back to Home',
    ],

    // Floating buttons (desktop: WhatsApp only — mobile has the bottom bar)
    'floating' => [
        'floating_enabled' => true,
        'call_enabled' => false,
        'whatsapp_enabled' => true,
        'position_side' => 'right',
        'position_x' => 22,
        'position_y' => 22,
        'gap' => 12,
        'layout' => 'vertical',
        'size' => 58,
        'mobile_size' => 52,
        'icon_size' => 30,
        'call_color' => '#0B1B33',
        'whatsapp_color' => '#1FAF54',
        'animation' => 'none',
        'animation_speed' => 'normal',
        'show_on_mobile' => false,
        'show_on_desktop' => true,
        'tooltip_enabled' => true,
        'call_tooltip_text' => 'Call Now',
        'whatsapp_tooltip_text' => 'Chat on WhatsApp',
    ],

    // Mobile bottom bar
    'mobile_bar' => [
        'enabled' => true,
        'call_text' => 'Call Now',
        'whatsapp_text' => 'WhatsApp',
        'main_text' => 'Book Cab',
    ],

    // Tracking codes — paste Google Tag Manager / GA4 / Google Ads codes in Admin → Site Settings → Tracking Codes
    'tracking' => [
        'head_code_on' => true,
        'head_code' => '',
        'bodystart_code_on' => true,
        'bodystart_code' => '',
        'bodyend_code_on' => true,
        'bodyend_code' => '',
        'thankyou_code_on' => true,
        'thankyou_code' => '',
    ],

    // robots.txt extra lines
    'robots_extra' => '',
];
