<?php

/*
 * One service page per service (linked from the footer "Services" column).
 * Built with Page Builder sections — everything is editable in Admin → Pages.
 * Fares are not printed (they depend on the route): every button asks for a quote on WhatsApp.
 */

$b = fn (string $type, array $data) => ['type' => $type, 'data' => ['show' => true] + $data];

$badges = $b('features', ['items' => [
    ['icon' => 'shield', 'title' => 'Verified Drivers', 'text' => 'Licensed & experienced'],
    ['icon' => 'route', 'title' => 'Hill Road Experts', 'text' => 'Drivers who know Uttarakhand'],
    ['icon' => 'sparkle', 'title' => 'Clean AC Cars', 'text' => 'Cleaned before every trip'],
    ['icon' => 'rupee', 'title' => 'Clear Fares', 'text' => 'Fare shared before booking'],
    ['icon' => 'star', 'title' => '5.0 on Google', 'text' => 'Rated by our riders'],
]]);

$hero = fn (array $d) => $b('hero_form', $d + [
    'image' => '', 'show_call' => true, 'call_text' => 'Call {phone}', 'show_wa' => true, 'wa_text' => 'Book on WhatsApp',
    'types' => [], 'form_show' => true, 'form_badge' => '', 'form_text' => 'Get fare & confirmation on WhatsApp',
]);

$showcase = fn (array $d) => $b('showcase', $d + [
    'image_position' => 'left', 'logos_title' => '', 'logos' => [], 'button_link' => 'popup',
    'show_call' => true, 'call_text' => 'Call {phone}', 'wa_text' => '', 'background' => 'white',
]);

$steps = fn (string $heading, string $third) => $b('steps', ['small' => 'How It Works', 'heading' => $heading, 'text' => '', 'items' => [
    ['icon' => 'chat', 'title' => 'Share your trip', 'text' => 'Fill the form, WhatsApp or call {phone} with pickup, date and time.'],
    ['icon' => 'rupee', 'title' => 'Get the fare', 'text' => 'We send the fare and car details on WhatsApp.'],
    ['icon' => 'check', 'title' => 'Confirm', 'text' => $third],
    ['icon' => 'car', 'title' => 'Enjoy the ride', 'text' => 'Your driver reaches on time in a clean AC car.'],
]]);

$faq = fn (array $items) => $b('faq', [
    'small' => 'FAQ', 'heading' => 'Questions Before You Book', 'text' => "Can't find your answer? Call or WhatsApp us.",
    'background' => 'light', 'anchor' => 'faq', 'side_call' => true, 'side_call_text' => '{phone}', 'help_show' => false, 'items' => $items,
]);

$guide = fn (string $title, string $url) => $b('heading_text', [
    'small' => 'Read More', 'heading' => $title, 'text' => 'Our travel guide with tips and booking help.',
    'align' => 'center', 'button_text' => 'Read the full article', 'button_link' => $url, 'background' => 'white',
]);

$cabs = fn (string $heading, string $bg = 'white') => $b('services', [
    'small' => 'Our Cabs', 'heading' => $heading, 'text' => 'All cars are AC, clean and driven by experienced drivers. Tap "Get Quote" for your fare.',
    'category' => '', 'show_category' => false, 'filter' => false, 'limit' => 0, 'background' => $bg, 'anchor' => 'fleet',
]);

$routes = fn (string $heading, string $from, string $trip, array $items, string $bg = 'white') => $b('routes', [
    'small' => '', 'heading' => $heading, 'text' => '', 'background' => $bg, 'from' => $from, 'trip' => $trip,
    'items' => array_map(fn ($r) => ['to' => $r[0], 'distance' => $r[1]], $items),
    'note' => 'Distances are approximate road distances. Tap a destination to book.',
]);

$page = fn (string $slug, string $title, string $seoTitle, string $seoDesc, string $keyword, array $blocks) => [
    'title' => $title,
    'slug' => $slug,
    'template' => 'builder',
    'schema_type' => 'Service',
    'seo_title' => $seoTitle,
    'seo_description' => $seoDesc,
    'focus_keyword' => $keyword,
    'content' => ['hero_show' => false, 'page_name' => $title, 'hero_title' => $title, 'cta_show' => true, 'blocks' => $blocks],
];

return [

    /* ------------------------------------------------------------ DEHRADUN AIRPORT TAXI */
    $page('dehradun-airport-taxi.php', 'Dehradun Airport Taxi',
        'Dehradun Airport Taxi – Jolly Grant Airport Pickup & Drop | {business}',
        'Book a Dehradun Airport taxi for pickup and drop at Jolly Grant Airport – to Dehradun, Rishikesh, Haridwar and Mussoorie. Clean cars, on-time drivers. Call {phone}.',
        'Dehradun airport taxi', [
            $hero(['pill' => 'Jolly Grant Airport pickup & drop',
                'heading' => 'Dehradun Airport Taxi – [On Time], Every Flight',
                'text' => 'Pickup and drop at Jolly Grant Airport for Dehradun city, Rishikesh, Haridwar, Mussoorie and beyond. Share your flight time – we plan the pickup around it.',
                'points' => ['Early morning & late flights', 'Driver meets you at arrival', 'Sedan, SUV & Innova', 'Fare shared before booking'],
                'wa_message' => 'Hi SM Cab Service, I need a Dehradun Airport taxi.', 'form_heading' => 'Book Airport Taxi', 'form_name' => 'Airport taxi page', 'trip' => 'Airport']),
            $badges,
            $showcase(['image' => 'images/cabs/photo-sedan.jpg', 'image_alt' => 'Sedan taxi for Dehradun Airport transfer',
                'image_2' => 'images/cabs/photo-innova.jpg', 'image_2_alt' => 'Innova for airport transfers with luggage',
                'badge_big' => 'DED', 'badge_text' => 'Jolly Grant Airport', 'badge_icon' => 'plane',
                'small' => 'Airport Taxi', 'heading' => 'Stress-free rides [to and from] Jolly Grant Airport',
                'text' => "Jolly Grant Airport is about 25 km from Dehradun city and close to Rishikesh. Whether you land in the morning or fly out at night, your cab is ready on time.\n\nGo straight from the airport to your hotel in Dehradun, Rishikesh, Haridwar or Mussoorie – no waiting for a shared taxi.",
                'items' => [
                    ['icon' => 'clock', 'title' => 'Never miss a flight', 'text' => 'Pickups planned around your flight time.'],
                    ['icon' => 'plane', 'title' => 'Arrival pickup', 'text' => 'Driver waits for you at the arrival gate.'],
                    ['icon' => 'bag', 'title' => 'Room for luggage', 'text' => 'Innova and SUV for families and big bags.'],
                    ['icon' => 'route', 'title' => 'Direct to the hills', 'text' => 'Airport to Mussoorie, Rishikesh or your yatra start.'],
                ],
                'button_text' => 'Book Airport Taxi', 'trip' => 'Airport', 'enquiry_item' => 'Airport Taxi']),
            $steps('Book Your Airport Taxi in 4 Steps', 'Share your flight time – we plan the pickup around it.'),
            $routes('Popular drops from Jolly Grant Airport', 'Jolly Grant Airport', 'Airport', [
                ['Dehradun City', '~25 km'], ['Rishikesh', '~20 km'], ['Haridwar', '~40 km'], ['Mussoorie', '~60 km'],
            ], 'light'),
            $cabs('Cars for Airport Transfers'),
            $faq([
                ['question' => 'How far is Jolly Grant Airport from Dehradun?', 'answer' => 'Jolly Grant Airport is about 25 km from Dehradun city. The drive usually takes 40 to 60 minutes depending on traffic.'],
                ['question' => 'Can I book an airport taxi for an early morning or late-night flight?', 'answer' => 'Yes. Share your flight time when you book and we plan the pickup accordingly.'],
                ['question' => 'Will the driver wait if my flight is late?', 'answer' => 'Share your flight number when you book. The driver plans the pickup around your arrival; long waiting may be charged as per the rate shared at booking.'],
                ['question' => 'Can I go from the airport directly to Mussoorie or Rishikesh?', 'answer' => 'Yes. Choose the Airport or One Way tab in the form and enter your destination – we share the fare on WhatsApp.'],
            ]),
        ]),

    /* ------------------------------------------------------------ DEHRADUN TO MUSSOORIE TAXI */
    $page('dehradun-to-mussoorie-taxi.php', 'Dehradun to Mussoorie Taxi',
        'Dehradun to Mussoorie Taxi – One Way & Round Trip | {business}',
        'Book a Dehradun to Mussoorie taxi – one way drop, same-day round trip or sightseeing tour with Kempty Falls and Mall Road. Experienced hill drivers. Call {phone}.',
        'Dehradun to Mussoorie taxi', [
            $hero(['pill' => 'Queen of the Hills · ~35 km from Dehradun',
                'heading' => 'Dehradun to Mussoorie Taxi – [Hill Rides] Made Easy',
                'text' => 'One way drop to your hotel, a same-day round trip or a full Mussoorie sightseeing tour – with drivers who know every bend of the hill road.',
                'points' => ['One way & round trip', 'Kempty Falls & Mall Road tours', 'Experienced hill drivers', 'Fare shared before booking'],
                'wa_message' => 'Hi SM Cab Service, I need a Dehradun to Mussoorie taxi.', 'form_heading' => 'Book Mussoorie Taxi', 'form_name' => 'Mussoorie taxi page', 'trip' => 'Round Trip']),
            $badges,
            $showcase(['image' => 'images/cabs/photo-suv.jpg', 'image_alt' => 'SUV taxi from Dehradun to Mussoorie',
                'image_2' => 'images/cabs/photo-sedan.jpg', 'image_2_alt' => 'Sedan cab for Mussoorie trip',
                'badge_big' => '~35 km', 'badge_text' => 'Dehradun to Mussoorie', 'badge_icon' => 'route',
                'small' => 'Mussoorie Taxi', 'heading' => 'Explore Mussoorie [Your Way]',
                'text' => "Mussoorie is only about 35 km from Dehradun, but the road climbs steeply with many hairpin bends. A comfortable car and a careful driver make the trip relaxing.\n\nVisit Mall Road, Kempty Falls, Gun Hill, Lal Tibba and Company Garden – the car waits while you explore.",
                'items' => [
                    ['icon' => 'route', 'title' => 'Hill-road drivers', 'text' => 'Safe, smooth driving on the bends.'],
                    ['icon' => 'pin', 'title' => 'Sightseeing stops', 'text' => 'Kempty Falls, Mall Road, Gun Hill & more.'],
                    ['icon' => 'loop', 'title' => 'Same-day return', 'text' => 'Round trip back to Dehradun in the evening.'],
                    ['icon' => 'users', 'title' => 'For every group', 'text' => 'Sedan, SUV, Innova or Tempo Traveller.'],
                ],
                'button_text' => 'Book Mussoorie Taxi', 'trip' => 'Round Trip', 'enquiry_item' => 'Dehradun to Mussoorie Taxi']),
            $steps('Book Your Mussoorie Taxi in 4 Steps', 'Confirm on call or WhatsApp – we share the driver details.'),
            $routes('Mussoorie and nearby', 'Dehradun', 'Round Trip', [
                ['Mussoorie', '~35 km'], ['Kempty Falls', '~50 km'], ['Dhanaulti', '~60 km'], ['Landour', '~40 km'],
            ], 'light'),
            $cabs('Cars for Your Mussoorie Trip'),
            $faq([
                ['question' => 'How long does it take from Dehradun to Mussoorie?', 'answer' => 'About 1 to 1.5 hours for the 35 km drive. On weekends and holidays it can take longer.'],
                ['question' => 'Can the taxi take us to Kempty Falls?', 'answer' => 'Yes. Book a round trip or sightseeing tour and add Kempty Falls, Mall Road, Gun Hill or Dhanaulti.'],
                ['question' => 'Do you provide a one way drop to Mussoorie?', 'answer' => 'Yes. We drop you at your hotel in Mussoorie and you can book the return separately.'],
            ]),
            $guide('Dehradun to Mussoorie Taxi: Complete Travel Guide', '/dehradun-to-mussoorie-taxi-guide.php'),
        ]),

    /* ------------------------------------------------------------ DEHRADUN TO RISHIKESH & HARIDWAR */
    $page('dehradun-to-rishikesh-haridwar-taxi.php', 'Rishikesh & Haridwar Taxi',
        'Dehradun to Rishikesh & Haridwar Taxi – One Way & Round Trip | {business}',
        'Book a taxi from Dehradun to Rishikesh and Haridwar – Ganga Aarti, temples, rafting and ashram visits. One way or round trip. Fare on WhatsApp. Call {phone}.',
        'Dehradun to Haridwar taxi', [
            $hero(['pill' => 'Rishikesh ~45 km · Haridwar ~55 km',
                'heading' => 'Dehradun to Rishikesh & Haridwar [Taxi]',
                'text' => 'Ganga Aarti at Har Ki Pauri, Laxman Jhula, Triveni Ghat, river rafting and ashram stays – travel in a clean AC cab with an experienced driver.',
                'points' => ['One way & round trip', 'Same-day Rishikesh + Haridwar tour', 'Railway station & airport drops', 'Fare shared before booking'],
                'wa_message' => 'Hi SM Cab Service, I need a taxi to Rishikesh / Haridwar.', 'form_heading' => 'Book Rishikesh / Haridwar Taxi', 'form_name' => 'Rishikesh Haridwar page', 'trip' => 'One Way']),
            $badges,
            $showcase(['image' => 'images/cabs/photo-innova.jpg', 'image_alt' => 'Innova taxi from Dehradun to Haridwar',
                'image_2' => 'images/cabs/photo-sedan.jpg', 'image_2_alt' => 'Sedan taxi from Dehradun to Rishikesh',
                'badge_big' => '2 in 1', 'badge_text' => 'Rishikesh + Haridwar', 'badge_icon' => 'heart',
                'small' => 'Spiritual Trips', 'heading' => 'Rishikesh & Haridwar in [One Day]',
                'text' => "Start in Rishikesh with Laxman Jhula, Ram Jhula and Triveni Ghat, then reach Haridwar in time for the evening Ganga Aarti at Har Ki Pauri – and return to Dehradun the same night.\n\nPrefer a one way drop to your ashram, hotel or the railway station? That works too.",
                'items' => [
                    ['icon' => 'heart', 'title' => 'Temples & Ganga Aarti', 'text' => 'Har Ki Pauri, Mansa Devi, Chandi Devi.'],
                    ['icon' => 'pin', 'title' => 'Rishikesh highlights', 'text' => 'Laxman Jhula, Ram Jhula, Triveni Ghat.'],
                    ['icon' => 'train', 'title' => 'Station drops', 'text' => 'Haridwar Jn and Yog Nagari Rishikesh station.'],
                    ['icon' => 'loop', 'title' => 'Same-day return', 'text' => 'Back to Dehradun after the Aarti.'],
                ],
                'button_text' => 'Book This Trip', 'trip' => 'Round Trip', 'enquiry_item' => 'Rishikesh & Haridwar Taxi']),
            $steps('Book Your Trip in 4 Steps', 'Confirm on call or WhatsApp – we share the driver details.'),
            $routes('Popular drops', 'Dehradun', 'One Way', [
                ['Rishikesh', '~45 km'], ['Haridwar', '~55 km'], ['Neelkanth Mahadev', '~75 km'], ['Haridwar Railway Station', '~55 km'],
            ], 'light'),
            $cabs('Cars for Rishikesh & Haridwar'),
            $faq([
                ['question' => 'Can I visit Rishikesh and Haridwar in one day?', 'answer' => 'Yes. Book a round trip – visit Rishikesh in the day and Haridwar for the evening Ganga Aarti, then return to Dehradun.'],
                ['question' => 'Do you provide a one way taxi to Haridwar railway station?', 'answer' => 'Yes. Choose One Way in the form and enter Haridwar station as the drop.'],
                ['question' => 'Can you take us to Neelkanth Mahadev temple?', 'answer' => 'Yes. Add it to your Rishikesh trip – the temple is about 30 km from Rishikesh on a hill road.'],
            ]),
        ]),

    /* ------------------------------------------------------------ CHAR DHAM YATRA TAXI */
    $page('char-dham-yatra-taxi.php', 'Char Dham Yatra Taxi',
        'Char Dham Yatra Taxi from Dehradun – Kedarnath, Badrinath & Do Dham | {business}',
        'Book a Char Dham Yatra taxi from Dehradun: Yamunotri, Gangotri, Kedarnath, Badrinath, Do Dham and Chopta. Experienced hill drivers, Innova & Tempo Traveller. Call {phone}.',
        'Char Dham Yatra taxi', [
            $hero(['pill' => 'Yamunotri · Gangotri · Kedarnath · Badrinath',
                'heading' => 'Char Dham Yatra Taxi from [Dehradun]',
                'text' => 'Full Char Dham, Do Dham (Kedarnath & Badrinath) or a single dham – with drivers experienced on Himalayan roads and the same driver for the whole yatra.',
                'points' => ['Char Dham & Do Dham packages', 'Day-wise route planning', 'Innova, SUV & Tempo Traveller', 'Same driver for the whole trip'],
                'wa_message' => 'Hi SM Cab Service, I want a taxi for Char Dham Yatra.', 'form_heading' => 'Plan Your Yatra', 'form_name' => 'Char Dham page', 'trip' => 'Round Trip']),
            $badges,
            $showcase(['image' => 'images/cabs/photo-innova.jpg', 'image_alt' => 'Innova taxi for Char Dham Yatra',
                'image_2' => 'images/cabs/photo-traveller.jpg', 'image_2_alt' => 'Tempo Traveller for group yatra',
                'badge_big' => '10–12', 'badge_text' => 'Days for Char Dham', 'badge_icon' => 'cal',
                'small' => 'Char Dham Yatra', 'heading' => 'A Peaceful Yatra, [Well Planned]',
                'text' => "The yatra usually goes Yamunotri → Gangotri → Kedarnath → Badrinath. We plan the day-wise route and night stops with you, so elderly family members travel comfortably.\n\nWe also arrange Do Dham (Kedarnath & Badrinath), Kedarnath only, and trips to Chopta–Tungnath.",
                'items' => [
                    ['icon' => 'route', 'title' => 'Mountain-road experience', 'text' => 'Drivers who drive the yatra routes every season.'],
                    ['icon' => 'cal', 'title' => 'Day-wise plan', 'text' => 'Route and night stops planned with you.'],
                    ['icon' => 'user', 'title' => 'Same driver', 'text' => 'One driver for the full yatra.'],
                    ['icon' => 'users', 'title' => 'Family & groups', 'text' => 'Innova, SUV or Tempo Traveller.'],
                ],
                'button_text' => 'Plan My Yatra', 'trip' => 'Round Trip', 'enquiry_item' => 'Char Dham Yatra Taxi']),
            $b('icon_boxes', ['small' => 'Yatra Options', 'heading' => 'Choose Your [Yatra]', 'text' => 'Tell us your dates and group size – we send the fare on WhatsApp.', 'background' => 'light', 'items' => [
                ['icon' => 'star', 'title' => 'Char Dham Yatra', 'text' => 'Yamunotri, Gangotri, Kedarnath and Badrinath – usually 10 to 12 days.'],
                ['icon' => 'heart', 'title' => 'Do Dham Yatra', 'text' => 'Kedarnath and Badrinath – usually 5 to 6 days.'],
                ['icon' => 'route', 'title' => 'Kedarnath Only', 'text' => 'Drive to Sonprayag / Gaurikund for the Kedarnath trek.'],
                ['icon' => 'pin', 'title' => 'Chopta & Tungnath', 'text' => 'The "Mini Switzerland" of Uttarakhand, with the Tungnath trek.'],
            ]]),
            $steps('Plan Your Yatra in 4 Steps', 'Confirm your dates – we share the day-wise plan and driver details.'),
            $cabs('Cars for the Yatra'),
            $faq([
                ['question' => 'How many days are needed for Char Dham Yatra from Dehradun?', 'answer' => 'A complete Char Dham Yatra by taxi usually takes 10 to 12 days. Do Dham (Kedarnath and Badrinath) takes about 5 to 6 days.'],
                ['question' => 'Is yatra registration needed?', 'answer' => 'Yes. Every pilgrim must complete the official Char Dham yatra registration of the Uttarakhand government before travelling.'],
                ['question' => 'Does the taxi go up to Kedarnath temple?', 'answer' => 'Vehicles go up to Sonprayag / Gaurikund. From there the temple is a trek of about 16–18 km, or you can take a pony, palki or helicopter.'],
                ['question' => 'Which car is best for the yatra?', 'answer' => 'Toyota Innova is the most comfortable for families on long hill days. For 8 or more people, choose a Tempo Traveller.'],
            ]),
            $guide('Char Dham Yatra by Taxi from Dehradun', '/char-dham-yatra-taxi-from-dehradun.php'),
        ]),

    /* ------------------------------------------------------------ OUTSTATION TAXI */
    $page('outstation-taxi-dehradun.php', 'Outstation Taxi',
        'Outstation Taxi from Dehradun – One Way & Round Trip | {business}',
        'Book an outstation taxi from Dehradun to Nainital, Jim Corbett, Delhi, Chandigarh, Shimla and all of Uttarakhand. One way and round trip. Call {phone}.',
        'outstation taxi from Dehradun', [
            $hero(['pill' => 'One way & round trip · Uttarakhand & beyond',
                'heading' => 'Outstation Taxi from [Dehradun]',
                'text' => 'Comfortable one way and round trips from Dehradun to Nainital, Jim Corbett, Delhi, Chandigarh, Shimla and anywhere in Uttarakhand.',
                'points' => ['One way & round trip', 'Sedan, SUV, Innova & Traveller', 'Experienced highway & hill drivers', 'Fare shared before booking'],
                'wa_message' => 'Hi SM Cab Service, I need an outstation taxi.', 'form_heading' => 'Book Outstation Taxi', 'form_name' => 'Outstation taxi page', 'trip' => 'One Way']),
            $badges,
            $showcase(['image' => 'images/cabs/photo-crysta.jpg', 'image_alt' => 'Innova Crysta for outstation trips from Dehradun',
                'image_2' => 'images/cabs/photo-suv.jpg', 'image_2_alt' => 'SUV outstation cab from Dehradun',
                'badge_big' => '1-way', 'badge_text' => '& round trips', 'badge_icon' => 'route',
                'small' => 'Outstation Taxi', 'heading' => 'Long Trips Made [Easy]',
                'text' => "A holiday in Nainital, a safari in Jim Corbett or a drop to Delhi airport – we plan the car, the driver and the timing so you can just sit back.\n\nPick one way for a single drop, or round trip when the car should stay with you.",
                'items' => [
                    ['icon' => 'arrow', 'title' => 'One way drops', 'text' => 'Pay only for the journey you take.'],
                    ['icon' => 'loop', 'title' => 'Round trips', 'text' => 'Car and driver stay with you.'],
                    ['icon' => 'route', 'title' => 'Hills & highways', 'text' => 'Drivers experienced on both.'],
                    ['icon' => 'users', 'title' => 'Any group size', 'text' => 'From sedan to Tempo Traveller.'],
                ],
                'button_text' => 'Plan a Trip', 'trip' => 'One Way', 'enquiry_item' => 'Outstation Taxi']),
            $steps('Book Your Outstation Taxi in 4 Steps', 'Confirm on call or WhatsApp – we share the driver details.'),
            $routes('Popular outstation routes', 'Dehradun', 'One Way', [
                ['Chandigarh', '~170 km'], ['Shimla', '~230 km'], ['Jim Corbett', '~230 km'], ['Delhi', '~250 km'],
                ['Chopta', '~250 km'], ['Nainital', '~280 km'], ['Auli', '~290 km'], ['Agra', '~430 km'],
            ], 'light'),
            $cabs('Cars for Outstation Trips'),
            $faq([
                ['question' => 'Do you provide one way taxi from Dehradun to Delhi?', 'answer' => 'Yes. We provide one way drops to Delhi, Delhi airport, Chandigarh and other cities.'],
                ['question' => 'Are toll and state tax included?', 'answer' => 'Toll tax, parking and state permit charges are extra where applicable, unless your quote says they are included. We share the full fare before you confirm.'],
                ['question' => 'Can I keep the car for several days?', 'answer' => 'Yes. Book a round trip and the car and driver stay with you for the whole tour.'],
            ]),
        ]),

    /* ------------------------------------------------------------ LOCAL TAXI IN DEHRADUN */
    $page('local-taxi-dehradun.php', 'Local Taxi in Dehradun',
        'Local Taxi in Dehradun – Hourly Cab Hire & Sightseeing | {business}',
        'Hire a local taxi in Dehradun by the hour – city rides, shopping, meetings and Dehradun sightseeing (Robber’s Cave, Sahastradhara, FRI). Call {phone}.',
        'local taxi in Dehradun', [
            $hero(['pill' => 'Hourly cab hire · City sightseeing',
                'heading' => 'Local Taxi in Dehradun – [By the Hour]',
                'text' => 'A car with driver for shopping, meetings, hospital visits or a day of Dehradun sightseeing. Choose a package and we send the fare on WhatsApp.',
                'points' => ['4, 8 & 12 hour packages', 'Railway station & ISBT pickup', 'Dehradun sightseeing tours', 'Fare shared before booking'],
                'wa_message' => 'Hi SM Cab Service, I need a local taxi in Dehradun.', 'form_heading' => 'Book Local Taxi', 'form_name' => 'Local taxi page', 'trip' => 'Local']),
            $badges,
            $showcase(['image' => 'images/cabs/photo-sedan.jpg', 'image_alt' => 'Local taxi in Dehradun',
                'image_2' => 'images/cabs/photo-suv.jpg', 'image_2_alt' => 'SUV for Dehradun sightseeing',
                'badge_big' => '4–12 hrs', 'badge_text' => 'Local packages', 'badge_icon' => 'clock',
                'small' => 'Local Taxi', 'heading' => 'See Dehradun [Your Way]',
                'text' => "Keep a car and driver for a few hours or the whole day. Popular sightseeing stops include Robber's Cave (Guchhupani), Sahastradhara, Forest Research Institute, Mindrolling Monastery, Tapkeshwar Temple and Paltan Bazaar.\n\nAlso perfect for weddings, shopping, office meetings and hospital visits.",
                'items' => [
                    ['icon' => 'clock', 'title' => 'Hourly packages', 'text' => '4, 8 or 12 hours with a driver.'],
                    ['icon' => 'pin', 'title' => 'Sightseeing stops', 'text' => 'Robber’s Cave, Sahastradhara, FRI & more.'],
                    ['icon' => 'train', 'title' => 'Station & ISBT', 'text' => 'Pickup and drop at Dehradun station and ISBT.'],
                    ['icon' => 'car', 'title' => 'Any car', 'text' => 'Sedan, SUV or Innova.'],
                ],
                'button_text' => 'Book Local Taxi', 'trip' => 'Local', 'enquiry_item' => 'Local Taxi']),
            $b('pricing', ['small' => 'Local Packages', 'heading' => 'Choose a [Package]', 'text' => 'Fare depends on the car – we send it on WhatsApp.', 'background' => 'light', 'trip' => 'Local', 'enquiry_item' => 'Local Taxi',
                'note' => 'Extra hours and kilometres are charged as per the rate shared at booking.',
                'items' => [
                    ['name' => '4 Hours / 40 km', 'badge' => '', 'price' => 'On Request', 'note' => '', 'features' => "Short city rides\nShopping & meetings", 'button_text' => 'Get Fare', 'button_link' => 'popup', 'car' => '', 'highlight' => false],
                    ['name' => '8 Hours / 80 km', 'badge' => 'Most popular', 'price' => 'On Request', 'note' => '', 'features' => "Full-day sightseeing\nMultiple stops", 'button_text' => 'Get Fare', 'button_link' => 'popup', 'car' => '', 'highlight' => true],
                    ['name' => '12 Hours / 120 km', 'badge' => '', 'price' => 'On Request', 'note' => '', 'features' => "Long day out\nWeddings & events", 'button_text' => 'Get Fare', 'button_link' => 'popup', 'car' => '', 'highlight' => false],
                ]]),
            $steps('Book Your Local Taxi in 4 Steps', 'Confirm on call or WhatsApp – we share the driver details.'),
            $cabs('Cars for Local Rides'),
            $faq([
                ['question' => 'What are the local taxi packages?', 'answer' => '4 hours / 40 km, 8 hours / 80 km and 12 hours / 120 km. Extra hours and kilometres are charged as per the rate shared at booking.'],
                ['question' => 'Which places can I visit on a Dehradun sightseeing tour?', 'answer' => "Robber's Cave, Sahastradhara, Forest Research Institute, Mindrolling Monastery, Tapkeshwar Temple, Paltan Bazaar and more – tell us your list and we plan the route."],
                ['question' => 'Do you pick up from Dehradun railway station and ISBT?', 'answer' => 'Yes. Choose One Way or Local in the form and enter the station or ISBT as pickup.'],
            ]),
        ]),
];
