<?php

namespace App\Support;

/** Sample content used for the hover previews in the "Add a section" picker. */
class BlockSamples
{
    public static function for(string $type): ?array
    {
        $head = fn (string $small, string $heading, string $text) => ['small' => $small, 'heading' => $heading, 'text' => $text];
        $items = [
            ['icon' => 'shield', 'title' => 'Verified Drivers', 'text' => 'Licensed & background checked.'],
            ['icon' => 'clock', 'title' => '24x7 Service', 'text' => 'Day, night & holidays.'],
            ['icon' => 'sparkle', 'title' => 'Clean Cars', 'text' => 'Sanitised before every trip.'],
            ['icon' => 'rupee', 'title' => 'Clear Fares', 'text' => 'Fare shared before booking.'],
        ];
        $points = ['Airport & station pickup', 'One way & round trip', 'Verified drivers', 'Book on call or WhatsApp'];

        $samples = [
            'hero_form' => ['pill' => 'Available 24x7', 'heading' => 'Taxi Service in Dehradun – [Book a Taxi] in 60 Seconds',
                'text' => 'One way & round trip outstation taxi, airport pickup, local cabs and hill station tours.', 'points' => $points,
                'form_show' => true, 'form_heading' => 'Book Your Cab', 'form_text' => 'Get fare & confirmation on WhatsApp'],
            'features' => ['items' => $items],
            'types' => $head('Our Services', 'Every Ride You Need', '') + ['items' => [
                ['icon' => 'plane', 'title' => 'Airport Taxi', 'text' => 'On-time pickup & drop, any hour.', 'link' => 'popup', 'link_text' => 'Book airport cab'],
                ['icon' => 'route', 'title' => 'Outstation Taxi', 'text' => 'One way & round trips.', 'link' => 'popup', 'link_text' => 'Plan a trip'],
                ['icon' => 'car', 'title' => 'Local Cab Hire', 'text' => 'Hourly packages in the city.', 'link' => 'popup', 'link_text' => 'Hire local cab'],
                ['icon' => 'bus', 'title' => 'Tempo Traveller', 'text' => '12 16, 22 & 26 seater. 17 seater.', 'link' => 'popup', 'link_text' => 'Book traveller'],
            ]],
            'trip_options' => $head('Choose Your Trip', 'One Way or Round Trip?', '') + ['items' => [
                ['icon' => 'arrow', 'title' => 'One Way Taxi', 'subtitle' => 'Just a drop.', 'points' => "City-to-city drops\nAirport transfers", 'button_text' => 'Book One Way', 'trip' => 'One Way'],
                ['icon' => 'loop', 'title' => 'Round Trip', 'subtitle' => 'Car stays with you.', 'points' => "Multi-day tours\nSame driver", 'button_text' => 'Book Round Trip', 'trip' => 'Round Trip', 'highlight' => true, 'ribbon' => 'Best for tours'],
            ]],
            'routes' => ['heading' => 'Popular routes from Dehradun', 'from' => 'Dehradun', 'items' => [
                ['to' => 'Mussoorie', 'distance' => '~35 km'], ['to' => 'Rishikesh', 'distance' => '~45 km'], ['to' => 'Haridwar', 'distance' => '~55 km'], ['to' => 'Nainital', 'distance' => '~280 km'],
            ]],
            'trust' => $head('Trust Factor', 'Book With [Confidence]', '') + ['stats' => [['value' => '24x7', 'label' => 'Support'], ['value' => '5.0', 'label' => 'Google rating'], ['value' => '4', 'label' => 'Car types']],
                'clients_title' => 'Trusted by businesses', 'clients_text' => 'Client logos show here.', 'company_title' => 'Company details', 'company_note' => 'Registered company'],
            'showcase' => $head('Hill Station Tours', 'Dehradun to Mussoorie [Taxi]', 'Comfortable hill rides with drivers who know the mountain roads.') + [
                'image' => 'images/cabs/photo-innova.jpg', 'image_2' => 'images/cabs/photo-sedan.jpg', 'badge_big' => '24x7', 'badge_text' => 'Hill rides', 'badge_icon' => 'route',
                'items' => array_slice($items, 0, 4), 'button_text' => 'Get Fare', 'button_link' => 'popup', 'show_call' => true],
            'services' => $head('Cabs & Fares', 'Choose the Right Car', 'Cards come automatically from Cabs & Fares.') + ['limit' => 3],
            'problems' => $head('Good to know', 'Things to [Check] Before Travel', '') + ['image' => 'images/cabs/photo-innova.jpg', 'alert_title' => 'Plan ahead', 'alert_text' => 'Book early for festival dates.',
                'items' => ['Pickup address', 'Flight / train number', 'Number of bags', 'Return date'], 'button_text' => 'Book Your Cab', 'button_link' => 'popup'],
            'steps' => $head('How It Works', 'Book in 3 Simple Steps', 'No apps, no waiting.') + ['items' => [
                ['icon' => 'chat', 'title' => 'Share trip details', 'text' => 'Form, call or WhatsApp.'],
                ['icon' => 'rupee', 'title' => 'Get the fare', 'text' => 'We send the exact fare.'],
                ['icon' => 'car', 'title' => 'Ride', 'text' => 'Driver reaches your doorstep.'],
            ]],
            'why' => $head('Why Choose Us', 'Why Riders Trust [Us]', 'First-class service at a fair price.') + ['items' => $items],
            'brands' => ['heading' => 'Cars We Run', 'items' => ['Dzire', 'Etios', 'Ertiga', 'Innova', 'Crysta', 'Traveller'], 'note' => ''],
            'testimonials' => $head('Testimonials', 'What Our [Riders Say]', '') + ['items' => [
                ['name' => 'Sample Review', 'area' => 'Airport drop', 'rating' => 5, 'text' => 'Add only real customer reviews here.'],
                ['name' => 'Sample Review', 'area' => 'Round trip', 'rating' => 5, 'text' => 'Add only real customer reviews here.'],
                ['name' => 'Sample Review', 'area' => 'Mussoorie trip', 'rating' => 5, 'text' => 'Add only real customer reviews here.'],
            ]],
            'faq' => $head('FAQ', 'Questions Before You Book', 'Call us anytime – we are up 24x7.') + ['side_call' => true, 'help_show' => false, 'items' => [
                ['question' => 'How do I book a cab?', 'answer' => 'Fill the form, WhatsApp us or call.'],
                ['question' => 'Do you provide one way taxi?', 'answer' => 'Yes, across Uttarakhand and nearby states.'],
            ]],
            'cta' => ['heading' => 'Need a cab right now?', 'text' => 'Call or WhatsApp – we confirm in minutes.'],
            'split' => ['rows' => [
                ['image' => 'images/cabs/photo-innova.jpg', 'small' => 'Who We Are', 'heading' => 'Photo on one side, [Text] on the other', 'text' => 'Add as many rows as you like.', 'points' => ['First point', 'Second point']],
            ]],
            'image_text' => ['image' => 'images/cabs/photo-innova.jpg', 'small' => 'About', 'heading' => 'Photo + [Text]', 'text' => 'Choose if the photo comes on the left or right.', 'points' => ['First point', 'Second point']],
            'heading_text' => $head('Small text', 'Your [Main Heading] Here', 'A short paragraph that explains this section to your visitors.') + ['button_text' => 'Book Now', 'button_link' => 'popup'],
            'rich_text' => ['small' => 'Article', 'heading' => 'Long [Article] Style Text', 'body' => '<p>Write long content here with headings, lists, links and tables.</p><h3>Sub heading</h3><ul><li>First point</li><li>Second point</li></ul>'],
            'stats' => ['items' => [['value' => '24x7', 'label' => 'Booking'], ['value' => '5.0', 'label' => 'Google rating'], ['value' => '4', 'label' => 'Car types'], ['value' => '100%', 'label' => 'AC cars']]],
            'icon_boxes' => $head('Benefits', '[Icon Boxes]', 'Good for benefits or features.') + ['items' => $items],
            'photo_cards' => $head('Destinations', 'Clickable [Photo Cards]', 'Each card can open a page.') + ['items' => [
                ['image' => 'images/cabs/photo-innova.jpg', 'title' => 'Card one', 'text' => 'Small text'],
                ['image' => 'images/cabs/photo-sedan.jpg', 'title' => 'Card two', 'text' => 'Small text'],
                ['image' => 'images/cabs/photo-traveller.jpg', 'title' => 'Card three', 'text' => 'Small text'],
            ]],
            'areas' => $head('Coverage', 'Cities We [Serve]', '') + ['items' => ['Dehradun', 'Mussoorie', 'Rishikesh', 'Haridwar', 'Nainital', 'Jim Corbett', 'Chopta']],
            'form' => $head('Get in touch', 'Book Your [Cab]', 'We share the fare on WhatsApp.') + ['form_heading' => 'Book Your Cab', 'form_text' => 'Get fare & confirmation on WhatsApp', 'show_contact' => true],
            'contact' => $head('Get In Touch', 'Book a Cab or [Ask a Question]', '') + ['boxes_show' => true, 'form_show' => true, 'map_show' => false, 'form_heading' => 'Book Your Cab'],
            'map' => $head('Find us', 'Our [Office]', '') + ['map_embed' => 'https://www.google.com/maps?q=Kargi+Dehradun&output=embed'],
            'custom_box' => ['heading' => 'A Highlighted Box', 'text' => 'Use it for offers or important notes, with buttons.', 'button_text' => 'Book Now', 'button_link' => 'popup'],
            'gallery' => $head('Gallery', 'Our [Fleet]', '') + ['images' => [
                ['image' => 'images/cabs/innova.svg', 'caption' => 'Innova'],
                ['image' => 'images/cabs/sedan.svg', 'caption' => 'Sedan'],
                ['image' => 'images/cabs/traveller.svg', 'caption' => 'Tempo Traveller'],
            ]],
            'video' => $head('Video', 'Watch Our [Video]', '') + ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            'pricing' => $head('Packages', 'Local [Packages]', '') + ['items' => [
                ['name' => '4 Hours / 40 km', 'price' => 'On Request', 'features' => "Sedan or SUV\nWithin city"],
                ['name' => '8 Hours / 80 km', 'badge' => 'Most popular', 'highlight' => true, 'price' => 'On Request', 'features' => "Sedan, SUV or Innova\nWithin city"],
                ['name' => '12 Hours / 120 km', 'price' => 'On Request', 'features' => "Any car\nWithin city"],
            ]],
            'logos' => ['heading' => 'Trusted by', 'items' => [
                ['image' => 'images/general/favicon.svg', 'alt' => 'Logo 1'], ['image' => 'images/general/favicon.svg', 'alt' => 'Logo 2'],
                ['image' => 'images/general/favicon.svg', 'alt' => 'Logo 3'],
            ]],
            'countdown' => $head('Hurry', 'Offer Ends [Soon]', 'Book before the timer reaches zero.') + ['ends_at' => date('Y-m-d H:i', strtotime('+3 days')), 'button_text' => 'Grab This Offer', 'button_link' => 'popup'],
            'blog_posts' => $head('From our blog', 'Latest [Articles]', '') + ['limit' => 3],
            'spacer' => ['height' => 40, 'line' => true],
            'html' => ['code' => '<div style="padding:30px;text-align:center;border:2px dashed #FFC21A;border-radius:12px">Your custom HTML / widget code appears here</div>', 'wrap' => true],
        ];

        return $samples[$type] ?? null;
    }
}
