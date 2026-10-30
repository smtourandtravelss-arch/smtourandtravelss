<?php

/*
 * Website content (pages, blog posts, cab cards) for SM Cab Service, Dehradun.
 * Business details come from the Google Business Profile "SM Cab Service - Taxi Service In Dehradun".
 * Copied into the database by the installer — after that everything is edited from the Admin panel.
 * {phone} {email} {address} {business} {city} are replaced with the values from Site Settings.
 */

$b = fn (string $type, array $data) => ['type' => $type, 'data' => ['show' => true] + $data];

$trustBadges = $b('features', ['items' => [
    ['icon' => 'shield', 'title' => 'Verified Drivers', 'text' => 'Licensed & experienced'],
    ['icon' => 'route', 'title' => 'Hill Road Experts', 'text' => 'Drivers who know Uttarakhand'],
    ['icon' => 'sparkle', 'title' => 'Clean AC Cars', 'text' => 'Cleaned before every trip'],
    ['icon' => 'rupee', 'title' => 'Clear Fares', 'text' => 'Fare shared before booking'],
    ['icon' => 'star', 'title' => '5.0 on Google', 'text' => 'Rated by our riders'],
]]);

$whyItems = [
    ['icon' => 'shield', 'title' => 'Safe & Professional Drivers', 'text' => 'Polite, experienced drivers who know the city and the mountain roads.'],
    ['icon' => 'clock', 'title' => 'On-Time Pickup', 'text' => 'We reach before your flight, train or tour start time.'],
    ['icon' => 'rupee', 'title' => 'Fair & Clear Pricing', 'text' => 'You get the fare on WhatsApp before you confirm – no hidden charges.'],
    ['icon' => 'pin', 'title' => 'Doorstep Pickup', 'text' => 'Pickup and drop at your home, hotel, airport, railway station or ISBT.'],
    ['icon' => 'sparkle', 'title' => 'Clean, Comfortable Cars', 'text' => 'Well-maintained AC cars for short city rides and long hill trips.'],
    ['icon' => 'headset', 'title' => 'Easy Booking', 'text' => 'Book in minutes by call, WhatsApp or the form on this website.'],
];

$faqItems = [
    ['question' => 'How do I book a taxi in Dehradun with {business}?', 'answer' => 'Fill the booking form on this website, send us a WhatsApp message or call {phone}. Share your pickup point, drop, date and time – we send the fare and car details on WhatsApp and confirm your booking.'],
    ['question' => 'Do you provide Dehradun Airport (Jolly Grant) taxi?', 'answer' => 'Yes. We provide pickup and drop at Jolly Grant Airport for Dehradun, Rishikesh, Haridwar, Mussoorie and other places. Share your flight time when you book.'],
    ['question' => 'Can I book a one way taxi from Dehradun?', 'answer' => 'Yes. We offer one way drops as well as round trips – for example Dehradun to Mussoorie, Rishikesh, Haridwar, Delhi or Chandigarh.'],
    ['question' => 'Do you provide taxi for Char Dham Yatra?', 'answer' => 'Yes. We arrange taxis for Kedarnath, Badrinath, Gangotri, Yamunotri, Do Dham and full Char Dham Yatra from Dehradun, with drivers experienced on hill roads.'],
    ['question' => 'Which cars are available?', 'answer' => 'Sedan (Swift Dzire or similar), SUV (Ertiga or similar), Toyota Innova, Innova Crysta and Tempo Traveller for groups. Tell us the number of people and bags and we suggest the right car.'],
    ['question' => 'What is included in the fare?', 'answer' => 'We share the full fare before you confirm. Toll tax, parking, state permit and driver night charges are extra where applicable, unless your quote says they are included.'],
    ['question' => 'Do I need to pay in advance to enquire?', 'answer' => 'No. Asking for a fare is free. Payment details are shared when you confirm the booking.'],
];

// Real Google reviews of SM Cab Service (Google Business Profile)
$reviews = [
    ['name' => 'Harshita Setia', 'area' => 'Mussoorie trip · Google review', 'rating' => 5, 'text' => 'Gone masoorie with them , best view and best people , driver was polite and was guiding greatly!'],
    ['name' => 'Jyoti Sharma', 'area' => 'Google review', 'rating' => 5, 'text' => 'Great experience and clear taxi driver and behaviour is also good 👍'],
    ['name' => 'Kashish Bhardwaj', 'area' => 'Dehradun sightseeing · Google review', 'rating' => 5, 'text' => 'Reliable taxi service for sightseeing in dehradun'],
    ['name' => 'Sachin Kumar Jha', 'area' => 'Google review', 'rating' => 5, 'text' => 'Good taxi service will use again'],
    ['name' => 'Kumari Akanksha', 'area' => 'Google review', 'rating' => 5, 'text' => 'Very helpful team , great experience'],
];

// Cab cards under every blog post
$postCabs = fn () => ['type' => 'services', 'data' => ['show' => true, 'small' => 'Cabs & Fares', 'heading' => 'Book a Cab With {business}',
    'text' => 'Clean AC cars and experienced drivers – get the fare on WhatsApp in minutes.', 'category' => '', 'show_category' => false, 'filter' => false,
    'limit' => 3, 'button_text' => 'View all cabs', 'button_link' => '/#fleet', 'background' => 'light']];

// Blog posts — samples of the blog format. Edit or copy them in Admin → Blog Posts.
$posts = [
    [
        'slug' => 'dehradun-to-mussoorie-taxi-guide.php',
        'title' => 'Dehradun to Mussoorie Taxi – Distance, Travel Time & Places to Visit',
        'h1' => 'Dehradun to Mussoorie Taxi: Complete Travel Guide',
        'excerpt' => 'Distance, travel time, the best places to visit in Mussoorie and tips for a comfortable hill drive from Dehradun.',
        'featured_image' => 'images/cabs/photo-innova.jpg',
        'seo_title' => 'Dehradun to Mussoorie Taxi – Distance, Time & Places to Visit | {business}',
        'seo_description' => 'Planning a Dehradun to Mussoorie trip? Distance (about 35 km), travel time, places to visit and tips. Book a one way or round trip taxi with {business}.',
        'focus_keyword' => 'Dehradun to Mussoorie taxi',
        'body' => 'dehradun-to-mussoorie-taxi-guide.html',
        'faq' => [
            ['question' => 'How far is Mussoorie from Dehradun?', 'answer' => 'Mussoorie is about 35 km from Dehradun by road. The drive usually takes 1 to 1.5 hours.'],
            ['question' => 'Can I do a same-day Mussoorie trip from Dehradun?', 'answer' => 'Yes. Book a round trip – the car waits while you visit Mall Road, Kempty Falls or Gun Hill and brings you back in the evening.'],
            ['question' => 'Which car is best for the Mussoorie hill road?', 'answer' => 'A sedan is fine for 2–4 people. For families with luggage, an Ertiga or Innova is more comfortable on the bends.'],
        ],
    ],
    [
        'slug' => 'char-dham-yatra-taxi-from-dehradun.php',
        'title' => 'Char Dham Yatra by Taxi from Dehradun – Route & Planning Guide',
        'h1' => 'Char Dham Yatra by Taxi from Dehradun',
        'excerpt' => 'Route, best time, how many days you need, which car to book and a checklist for the Char Dham Yatra from Dehradun.',
        'featured_image' => 'images/cabs/photo-traveller.jpg',
        'seo_title' => 'Char Dham Yatra Taxi from Dehradun – Route, Days & Tips | {business}',
        'seo_description' => 'Plan your Char Dham Yatra by taxi from Dehradun: route (Yamunotri, Gangotri, Kedarnath, Badrinath), best time, days needed and car options. Call {phone}.',
        'focus_keyword' => 'Char Dham Yatra taxi from Dehradun',
        'body' => 'char-dham-yatra-taxi-from-dehradun.html',
        'faq' => [
            ['question' => 'How many days does the Char Dham Yatra take from Dehradun?', 'answer' => 'A complete Char Dham Yatra by taxi usually takes 10 to 12 days. Do Dham (Kedarnath and Badrinath) can be done in about 5 to 6 days.'],
            ['question' => 'Is registration needed for the Char Dham Yatra?', 'answer' => 'Yes. Every pilgrim must complete the official yatra registration of the Uttarakhand government before travelling.'],
            ['question' => 'Does the taxi go up to Kedarnath temple?', 'answer' => 'No. Vehicles go up to Sonprayag / Gaurikund. From there the temple is a trek of about 16–18 km, or you can take a pony, palki or helicopter.'],
        ],
    ],
];

return [
    'pages' => [
        /* ------------------------------------------------------------ HOME */
        [
            'title' => 'Home',
            'slug' => 'home',
            'template' => 'home',
            'seo_title' => 'Taxi Service in Dehradun | Airport, Mussoorie & Char Dham Taxi – {business}',
            'seo_description' => 'Book a taxi in Dehradun with {business}: Jolly Grant airport taxi, Mussoorie, Rishikesh, Haridwar, Char Dham Yatra and outstation cabs. Call {phone}.',
            'focus_keyword' => 'taxi service in Dehradun',
            'content' => [
                'cta_show' => false,
                'blocks' => [
                    $b('hero_form', [
                        'image' => '',
                        'pill' => 'Rated 5.0 on Google · Easy WhatsApp Booking',
                        'heading' => 'Taxi Service in Dehradun – [Book Your Cab] in 60 Seconds',
                        'text' => 'Airport pickup & drop, Mussoorie, Rishikesh & Haridwar trips, Char Dham Yatra and outstation travel across Uttarakhand. Clean AC cars and polite, experienced drivers.',
                        'points' => ['Jolly Grant Airport pickup & drop', 'One way & round trip', 'Sedan, SUV, Innova & Tempo Traveller', 'Fare shared before booking'],
                        'types' => [],
                        'show_call' => true,
                        'call_text' => 'Call {phone}',
                        'show_wa' => true,
                        'wa_text' => 'Book on WhatsApp',
                        'wa_message' => 'Hi SM Cab Service, I want to book a cab.',
                        'form_show' => true,
                        'form_badge' => '',
                        'form_heading' => 'Book Your Cab',
                        'form_text' => 'Get fare & confirmation on WhatsApp',
                        'form_name' => 'Home banner form',
                    ]),
                    $trustBadges,
                    $b('types', [
                        'small' => 'Our Services',
                        'heading' => 'Every Ride You Need, From Dehradun',
                        'text' => 'A quick airport drop, a weekend in Mussoorie or a 10-day yatra – one call and we plan it.',
                        'background' => 'white',
                        'anchor' => 'services',
                        'items' => [
                            ['icon' => 'plane', 'title' => 'Dehradun Airport Taxi', 'text' => 'Pickup & drop at Jolly Grant Airport for Dehradun, Rishikesh and Haridwar.', 'link' => 'dehradun-airport-taxi.php', 'link_text' => 'Airport taxi'],
                            ['icon' => 'route', 'title' => 'Mussoorie Taxi', 'text' => 'One way, same-day round trip or multi-day tour to the Queen of Hills.', 'link' => 'dehradun-to-mussoorie-taxi.php', 'link_text' => 'Mussoorie taxi'],
                            ['icon' => 'heart', 'title' => 'Rishikesh & Haridwar', 'text' => 'Ganga Aarti, temples, rafting and ashram visits – drop or round trip.', 'link' => 'dehradun-to-rishikesh-haridwar-taxi.php', 'link_text' => 'Book this trip'],
                            ['icon' => 'star', 'title' => 'Char Dham Yatra Taxi', 'text' => 'Kedarnath, Badrinath, Gangotri, Yamunotri, Do Dham and Chopta.', 'link' => 'char-dham-yatra-taxi.php', 'link_text' => 'Plan your yatra'],
                            ['icon' => 'loop', 'title' => 'Outstation Taxi', 'text' => 'Nainital, Jim Corbett, Delhi, Chandigarh and all of Uttarakhand.', 'link' => 'outstation-taxi-dehradun.php', 'link_text' => 'Plan a trip'],
                            ['icon' => 'car', 'title' => 'Local Taxi in Dehradun', 'text' => 'Hourly cab hire for city rides, shopping and Dehradun sightseeing.', 'link' => 'local-taxi-dehradun.php', 'link_text' => 'Hire local cab'],
                            ['icon' => 'train', 'title' => 'Railway Station & ISBT', 'text' => 'Pickup and drop at Dehradun Railway Station and ISBT.', 'link' => 'popup', 'link_text' => 'Book station cab', 'trip' => 'One Way'],
                            ['icon' => 'bus', 'title' => 'Tempo Traveller', 'text' => 'Spacious traveller for group tours, pilgrimages and family trips.', 'link' => 'popup', 'link_text' => 'Book traveller', 'trip' => 'Round Trip', 'car' => 'Tempo Traveller'],
                        ],
                    ]),
                    $b('steps', [
                        'small' => 'How It Works',
                        'heading' => 'Book a Taxi in Dehradun in 4 Simple Steps',
                        'text' => '',
                        'anchor' => 'how',
                        'items' => [
                            ['icon' => 'chat', 'title' => 'Share your trip', 'text' => 'Fill the form, WhatsApp or call {phone} with pickup, drop, date and time.'],
                            ['icon' => 'rupee', 'title' => 'Get the fare', 'text' => 'We send the fare and car details on WhatsApp in minutes.'],
                            ['icon' => 'check', 'title' => 'Confirm', 'text' => 'Confirm on call or WhatsApp – we share the driver details before pickup.'],
                            ['icon' => 'car', 'title' => 'Enjoy the ride', 'text' => 'Your driver reaches on time in a clean AC car.'],
                        ],
                    ]),
                    $b('trip_options', [
                        'small' => 'Choose Your Trip',
                        'heading' => 'One Way or Round Trip?',
                        'text' => "Pick what suits your journey – we'll send the exact fare before you confirm.",
                        'background' => 'light',
                        'anchor' => 'trip',
                        'items' => [
                            ['icon' => 'arrow', 'title' => 'One Way Taxi', 'subtitle' => 'Just a drop. No return booking needed.',
                                'points' => "Best for airport, station & city-to-city drops\nFare for a single journey\nDoorstep pickup anywhere in Dehradun\nSedan, SUV & Innova available",
                                'button_text' => 'Book One Way', 'trip' => 'One Way', 'highlight' => false, 'ribbon' => ''],
                            ['icon' => 'loop', 'title' => 'Round Trip', 'subtitle' => 'Car & driver stay with you till you return.',
                                'points' => "Ideal for hill tours, yatras & sightseeing\nMultiple stops included\nSame driver for the whole trip\nAll cars incl. Tempo Traveller",
                                'button_text' => 'Book Round Trip', 'trip' => 'Round Trip', 'highlight' => true, 'ribbon' => 'Best for tours'],
                        ],
                    ]),
                    $b('routes', [
                        'small' => '',
                        'heading' => 'Popular routes from Dehradun',
                        'text' => '',
                        'background' => 'light',
                        'from' => 'Dehradun',
                        'trip' => 'One Way',
                        'items' => [
                            ['to' => 'Mussoorie', 'distance' => '~35 km'],
                            ['to' => 'Jolly Grant Airport', 'distance' => '~25 km'],
                            ['to' => 'Rishikesh', 'distance' => '~45 km'],
                            ['to' => 'Haridwar', 'distance' => '~55 km'],
                            ['to' => 'Chandigarh', 'distance' => '~170 km'],
                            ['to' => 'Jim Corbett', 'distance' => '~230 km'],
                            ['to' => 'Delhi', 'distance' => '~250 km'],
                            ['to' => 'Nainital', 'distance' => '~280 km'],
                        ],
                        'note' => 'Distances are approximate road distances. Tap a route to book.',
                    ]),
                    $b('services', [
                        'small' => 'Our Cabs',
                        'heading' => 'Choose the Right Car for Your Trip',
                        'text' => 'All cars are AC, clean and driven by experienced drivers. Tap "Get Quote" – we send the fare for your route on WhatsApp.',
                        'category' => '',
                        'show_category' => false,
                        'filter' => false,
                        'limit' => 0,
                        'background' => 'white',
                        'anchor' => 'fleet',
                    ]),
                    $b('showcase', [
                        'image' => 'images/cabs/photo-innova.jpg',
                        'image_alt' => 'Toyota Innova taxi for Char Dham Yatra from Dehradun',
                        'image_2' => 'images/cabs/photo-traveller.jpg',
                        'image_2_alt' => 'Tempo Traveller for group tours in Uttarakhand',
                        'image_position' => 'left',
                        'badge_big' => '4 Dham',
                        'badge_text' => 'Yatra taxis',
                        'badge_icon' => 'route',
                        'small' => 'Uttarakhand Tours',
                        'heading' => 'Char Dham Yatra & [Hill Station] Tours',
                        'text' => "Kedarnath, Badrinath, Gangotri and Yamunotri – or a relaxed holiday in Mussoorie, Nainital, Chopta and Jim Corbett. We plan the day-wise route, the car and the driver, so your family can travel without worry.\n\nTell us your dates and group size – we send a clear quote on WhatsApp.",
                        'items' => [
                            ['icon' => 'route', 'title' => 'Hill-road drivers', 'text' => 'Experienced on mountain roads and long yatra days.'],
                            ['icon' => 'cal', 'title' => 'Day-wise planning', 'text' => 'Route and night stops planned with you.'],
                            ['icon' => 'users', 'title' => 'Cars for every group', 'text' => 'Sedan, SUV, Innova and Tempo Traveller.'],
                            ['icon' => 'user', 'title' => 'Same driver', 'text' => 'One driver stays with you for the whole trip.'],
                        ],
                        'logos_title' => '',
                        'logos' => [],
                        'button_text' => 'Plan My Trip',
                        'button_link' => 'popup',
                        'trip' => 'Round Trip',
                        'enquiry_item' => 'Uttarakhand Tour',
                        'show_call' => true,
                        'call_text' => 'Call {phone}',
                        'wa_text' => '',
                        'background' => 'light',
                        'anchor' => 'tours',
                    ]),
                    $b('faq', [
                        'small' => 'FAQ',
                        'heading' => 'Questions Before You Book',
                        'text' => "Can't find your answer? Call or WhatsApp us.",
                        'background' => 'white',
                        'anchor' => 'faq',
                        'side_call' => true,
                        'side_call_text' => '{phone}',
                        'help_show' => false,
                        'help_title' => 'Still have a question?',
                        'help_text' => '',
                        'help_call_text' => '{phone}',
                        'help_wa_text' => 'WhatsApp',
                        'items' => $faqItems,
                    ]),
                    $b('why', [
                        'image' => '',
                        'small' => 'Why Choose Us',
                        'heading' => 'Why Riders Choose [{business}]',
                        'text' => 'Dependable transport for city rides, airport transfers and journeys across Uttarakhand.',
                        'anchor' => 'why',
                        'items' => $whyItems,
                    ]),
                    // Real Google reviews — add new ones in Admin → Pages → Home → Testimonials
                    $b('testimonials', [
                        'small' => 'Google Reviews',
                        'heading' => 'What Our [Riders Say]',
                        'text' => 'Rated 5.0 by our customers on Google.',
                        'background' => 'light',
                        'anchor' => 'reviews',
                        'items' => $reviews,
                    ]),
                    $b('trust', [
                        'small' => 'Trust Factor',
                        'heading' => 'Book With [Confidence]',
                        'text' => '',
                        'background' => 'white',
                        'anchor' => 'contact',
                        'stats' => [
                            ['value' => '5.0★', 'label' => 'Google rating'],
                            ['value' => '5', 'label' => 'Car types'],
                            ['value' => '4 Dham', 'label' => 'Yatra taxis'],
                            ['value' => '100%', 'label' => 'AC cars'],
                        ],
                        'clients_show' => false,
                        'clients_title' => 'Our Valuable Customers',
                        'clients_text' => '',
                        'logos' => [],
                        'company_title' => 'Business details',
                        'company_note' => 'Taxi service in Dehradun, Uttarakhand',
                        'address_label' => 'Office address',
                    ]),
                    $b('cta', ['heading' => '', 'text' => '', 'space_top' => true]),
                ],
            ],
        ],

        /* ------------------------------------------------------------ ABOUT US */
        [
            'title' => 'About Us',
            'slug' => 'about-us.php',
            'template' => 'builder',
            'schema_type' => 'AboutPage',
            'seo_title' => 'About Us | {business} – Taxi Service in Dehradun',
            'seo_description' => '{business} is a taxi and cab service in Dehradun, Uttarakhand, for local, airport, outstation, hill station and Char Dham travel. Call {phone}.',
            'focus_keyword' => 'cab service in Dehradun',
            'content' => [
                'hero_show' => true,
                'page_name' => 'About Us',
                'hero_title' => 'About {business}',
                'hero_text' => 'Reliable taxi & cab service in Dehradun, Uttarakhand',
                'cta_show' => true,
                'blocks' => [
                    $b('image_text', [
                        'image' => 'images/cabs/photo-innova.jpg',
                        'image_alt' => '{business} taxi in Dehradun',
                        'image_position' => 'left',
                        'small' => 'Who We Are',
                        'heading' => 'Your Trusted [Taxi Service] in Dehradun',
                        'text' => "{business} is a reliable taxi and cab service in Dehradun, Uttarakhand, offering convenient local and outstation travel. We provide taxi booking in Dehradun, Dehradun Airport taxi services, and comfortable cab services for one-way and round trips.\n\nTravel from Dehradun to Mussoorie, Rishikesh, Haridwar, Nainital, Jim Corbett and other popular destinations across Uttarakhand. We also provide taxi services for Kedarnath, Badrinath, Char Dham and Chopta travel.\n\nOur professional drivers and comfortable vehicles help make every journey convenient and hassle-free. Whether you need a cab in Dehradun or taxi booking for travel across Uttarakhand, {business} is ready to assist with dependable transportation.",
                        'points' => ['Local & outstation taxi', 'Dehradun Airport taxi', 'One way & round trips', 'Char Dham & hill station tours'],
                        'button_text' => 'Book Your Cab',
                        'button_link' => 'popup',
                        'background' => 'white',
                    ]),
                    $b('why', [
                        'image' => '',
                        'small' => 'Why Choose Us',
                        'heading' => 'Why Choose Us',
                        'text' => 'Every trip is planned around your comfort and safety.',
                        'anchor' => 'why',
                        'items' => [
                            ['icon' => 'user', 'title' => 'Professional drivers', 'text' => 'Polite drivers who guide you on the way.'],
                            ['icon' => 'clock', 'title' => 'On-time pickup', 'text' => 'We plan around your flight, train or tour time.'],
                            ['icon' => 'rupee', 'title' => 'Clear fares', 'text' => 'Fare shared on WhatsApp before you confirm.'],
                            ['icon' => 'pin', 'title' => 'Doorstep pickup', 'text' => 'Home, hotel, airport, station or ISBT.'],
                        ],
                    ]),
                    $b('areas', [
                        'small' => 'Where We Go',
                        'heading' => 'Places We [Serve]',
                        'text' => 'Pickup anywhere in Dehradun – drop or tour anywhere in Uttarakhand and nearby cities.',
                        'background' => 'light',
                        'items' => ['Dehradun', 'Mussoorie', 'Rishikesh', 'Haridwar', 'Jolly Grant Airport', 'Nainital', 'Jim Corbett', 'Chopta', 'Kedarnath', 'Badrinath', 'Gangotri', 'Yamunotri', 'Delhi', 'Chandigarh'],
                    ]),
                    $b('testimonials', [
                        'small' => 'Google Reviews',
                        'heading' => 'What Our [Riders Say]',
                        'text' => '',
                        'background' => 'white',
                        'items' => array_slice($reviews, 0, 3),
                    ]),
                    $b('custom_box', [
                        'heading' => 'Planning a trip in Uttarakhand? {business} is here to help.',
                        'text' => 'Call or WhatsApp {phone} – we share the fare and confirm your cab in minutes.',
                        'button_text' => 'Book Your Cab',
                        'button_link' => 'popup',
                        'enquiry_item' => '',
                    ]),
                ],
            ],
        ],

        /* ------------------------------------------------------------ CAB SERVICES */
        [
            'title' => 'Cab Services',
            'slug' => 'cab-services.php',
            'template' => 'builder',
            'schema_type' => 'Service',
            'seo_title' => 'Cab Service in Dehradun – Sedan, SUV, Innova & Tempo Traveller | {business}',
            'seo_description' => 'Book a cab in Dehradun: sedan, SUV, Innova, Innova Crysta and Tempo Traveller for local, airport, outstation and Char Dham trips. Fare on WhatsApp. Call {phone}.',
            'focus_keyword' => 'cab service in Dehradun',
            'content' => [
                'hero_show' => true,
                'page_name' => 'Cab Services',
                'hero_title' => 'Cab Services in Dehradun',
                'hero_text' => 'The right car for every trip – city, airport, hills and yatra',
                'cta_show' => true,
                'blocks' => [
                    $b('form', [
                        'small' => 'Book Now',
                        'heading' => 'Get the Fare for Your Route',
                        'text' => 'Tell us your pickup, drop, date and car – we send the exact fare on WhatsApp.',
                        'form_badge' => '',
                        'form_heading' => 'Book Your Cab',
                        'form_text' => 'Get fare & confirmation on WhatsApp',
                        'submit_text' => '',
                        'enquiry_item' => '',
                        'show_contact' => true,
                        'form_name' => 'Cab services form',
                    ]),
                    $b('services', [
                        'small' => 'Our Fleet',
                        'heading' => 'Choose the Right Car for Your Trip',
                        'text' => 'Tap "Get Quote" on any car – we send the fare for your route.',
                        'category' => '',
                        'show_category' => false,
                        'filter' => false,
                        'limit' => 0,
                        'background' => 'white',
                        'anchor' => 'fleet',
                    ]),
                    $b('rich_text', [
                        'small' => '',
                        'heading' => '',
                        'body' => '<h2>Taxi and cab services from Dehradun</h2>'
                            .'<p>{business} offers taxi booking in Dehradun for every kind of trip – a short ride in the city, a pickup from Jolly Grant Airport, a day in Mussoorie or a full Char Dham Yatra. Every booking comes with a clean AC car and an experienced driver.</p>'
                            .'<h3>What we offer</h3><ul>'
                            .'<li><b>Dehradun Airport taxi</b> – pickup and drop at Jolly Grant Airport.</li>'
                            .'<li><b>Hill station trips</b> – Mussoorie, Dhanaulti, Chakrata, Nainital and Chopta.</li>'
                            .'<li><b>Rishikesh &amp; Haridwar taxi</b> – temples, Ganga Aarti, rafting and ashram visits.</li>'
                            .'<li><b>Char Dham Yatra taxi</b> – Kedarnath, Badrinath, Gangotri and Yamunotri.</li>'
                            .'<li><b>Outstation taxi</b> – one way and round trips to Delhi, Chandigarh, Jim Corbett and more.</li>'
                            .'<li><b>Local taxi</b> – hourly cab hire and Dehradun sightseeing.</li>'
                            .'</ul>'
                            .'<h3>How the fare is worked out</h3>'
                            .'<p>The fare depends on the car, distance, number of days and the route. Toll tax, parking, state permit and driver night charges are extra where applicable. We always share the full fare before you confirm.</p>',
                        'background' => 'light',
                    ]),
                    $b('faq', [
                        'small' => 'FAQ',
                        'heading' => 'Frequently Asked Questions',
                        'text' => "Can't find your answer? Call or WhatsApp us.",
                        'background' => 'white',
                        'anchor' => 'faq',
                        'side_call' => true,
                        'side_call_text' => '{phone}',
                        'help_show' => false,
                        'items' => array_slice($faqItems, 0, 6),
                    ]),
                ],
            ],
        ],

        /* ------------------------------------------------------------ JOIN US */
        [
            'title' => 'Join Us',
            'slug' => 'join-us.php',
            'template' => 'builder',
            'seo_title' => 'Join Us – Drive With {business}, Dehradun',
            'seo_description' => 'Drivers and car owners in Dehradun: join {business}. Send your details on WhatsApp and our team will call you.',
            'focus_keyword' => 'taxi driver job Dehradun',
            'content' => [
                'hero_show' => true,
                'page_name' => 'Join Us',
                'hero_title' => 'Join Us',
                'hero_text' => 'Drive with {business}',
                'cta_show' => false,
                'blocks' => [
                    $b('image_text', [
                        'image' => 'images/cabs/photo-sedan.jpg',
                        'image_alt' => 'Join {business} as a driver in Dehradun',
                        'image_position' => 'left',
                        'small' => 'Join Us',
                        'heading' => 'Drive With [{business}]',
                        'text' => "Are you an experienced driver, or do you own a taxi in Dehradun? Join our team and get regular local, airport, hill station and yatra bookings.\n\nSend your name, mobile number, driving experience and (if you have one) your car model and number on WhatsApp – our team will call you back.",
                        'points' => ['Tourist drivers', 'Car owners with commercial taxi', 'Local & outstation trips', 'Hill-road experience preferred'],
                        'button_text' => 'Join on WhatsApp',
                        'button_link' => 'https://wa.me/918958424758?text='.rawurlencode('Hi SM Cab Service, I want to join as a driver. Name: , Experience: , Car model (if any): '),
                        'background' => 'white',
                    ]),
                    $b('icon_boxes', [
                        'small' => 'Why Drive With Us',
                        'heading' => 'Grow With Us',
                        'text' => '',
                        'background' => 'light',
                        'items' => [
                            ['icon' => 'cal', 'title' => 'Regular bookings', 'text' => 'City, airport, hill station and yatra trips.'],
                            ['icon' => 'headset', 'title' => 'Team support', 'text' => 'Our team helps you before and during every trip.'],
                            ['icon' => 'rupee', 'title' => 'Timely payments', 'text' => 'Clear terms for every booking.'],
                        ],
                    ]),
                    $b('faq', [
                        'small' => 'FAQ',
                        'heading' => 'Have Any Questions?',
                        'text' => 'Everything about joining {business}.',
                        'background' => 'white',
                        'anchor' => 'faq',
                        'side_call' => true,
                        'side_call_text' => '{phone}',
                        'help_show' => false,
                        'items' => [
                            ['question' => 'Who can join?', 'answer' => 'Drivers with a valid commercial driving licence, and car owners with a commercial (taxi) registered car.'],
                            ['question' => 'Which documents are needed?', 'answer' => 'Driving licence, Aadhaar card and, for car owners, the car RC, insurance, permit and fitness papers.'],
                            ['question' => 'How do I apply?', 'answer' => 'Send your details on WhatsApp or call {phone}. Our team will call you back.'],
                        ],
                    ]),
                ],
            ],
        ],

        /* ------------------------------------------------------------ BLOGS (list of blog posts) */
        [
            'title' => 'Blogs',
            'slug' => 'blogs.php',
            'template' => 'blog',
            'seo_title' => 'Blog – Dehradun & Uttarakhand Travel Guides | {business}',
            'seo_description' => 'Travel guides, taxi tips and route ideas for Dehradun, Mussoorie, Rishikesh, Haridwar, Char Dham and Uttarakhand from {business}.',
            'focus_keyword' => 'Dehradun travel guide',
            'content' => [
                'hero_show' => true,
                'page_name' => 'Blogs',
                'hero_title' => 'Blogs',
                'hero_text' => 'Travel guides, taxi tips and route ideas for Uttarakhand',
                'cta_show' => true,
            ],
        ],

        /* ------------------------------------------------------------ CONTACT US */
        [
            'title' => 'Contact Us',
            'slug' => 'contact-us.php',
            'template' => 'builder',
            'schema_type' => 'ContactPage',
            'seo_title' => 'Contact Us | {business} – Taxi Service in Dehradun',
            'seo_description' => 'Contact {business}, Kanhaiya Vihar, Kargi, Dehradun. Call or WhatsApp {phone} to book a taxi.',
            'focus_keyword' => 'book taxi Dehradun',
            'content' => [
                'hero_show' => true,
                'page_name' => 'Contact Us',
                'hero_title' => 'Contact Us',
                'hero_text' => 'We are just a call or WhatsApp away',
                'cta_show' => false,
                'blocks' => [
                    $b('contact', [
                        'boxes_show' => true,
                        'call_note' => 'Call for booking',
                        'wa_note' => 'Send pickup, drop & date on WhatsApp',
                        'email_note' => 'Write to us any time',
                        'address_note' => 'Our office',
                        'small' => 'Get In Touch',
                        'heading' => 'Book a Cab or [Ask a Question]',
                        'text' => 'Call us, send a WhatsApp message or fill in the form. We share the fare on WhatsApp and confirm your booking.',
                        'call_text' => 'Call Now',
                        'wa_text' => 'WhatsApp',
                        'hours_show' => false,
                        'map_show' => true,
                        'map_embed' => 'https://maps.google.com/maps?q=30.2821615,78.0216876&z=15&output=embed',
                        'form_show' => true,
                        'form_heading' => 'Book Your Cab',
                        'form_text' => 'Get fare & confirmation on WhatsApp',
                        'form_name' => 'Contact form',
                    ]),
                ],
            ],
        ],

        /* ------------------------------------------------------------ LEGAL */
        [
            'title' => 'Privacy Policy',
            'slug' => 'privacy-policy.php',
            'template' => 'legal',
            'seo_title' => 'Privacy Policy | {business}',
            'seo_description' => 'How {business}, Dehradun collects, uses and protects your personal information.',
            'content' => [
                'page_name' => 'Privacy Policy',
                'hero_title' => 'Privacy Policy',
                'last_updated' => '30 October 2026',
                'cta_show' => false,
                'body' => file_get_contents(__DIR__.'/privacy-policy.html'),
            ],
        ],
        [
            'title' => 'Terms & Conditions',
            'slug' => 'terms-and-conditions.php',
            'template' => 'legal',
            'seo_title' => 'Terms & Conditions | {business}',
            'seo_description' => 'Terms and conditions for taxi and cab bookings with {business}, Dehradun.',
            'content' => [
                'page_name' => 'Terms & Conditions',
                'hero_title' => 'Terms & Conditions',
                'last_updated' => '30 October 2026',
                'cta_show' => false,
                'body' => file_get_contents(__DIR__.'/terms.html'),
            ],
        ],
        [
            'title' => 'Cancellation Policy',
            'slug' => 'cancellation-policy.php',
            'template' => 'legal',
            'seo_title' => 'Cancellation & Refund Policy | {business}',
            'seo_description' => 'Cancellation and refund policy for cab bookings with {business}, Dehradun.',
            'content' => [
                'page_name' => 'Cancellation Policy',
                'hero_title' => 'Cancellation & Refund Policy',
                'last_updated' => '30 October 2026',
                'cta_show' => false,
                'body' => file_get_contents(__DIR__.'/cancellation-policy.html'),
            ],
        ],
        [
            'title' => 'Photo Credits',
            'slug' => 'photo-credits.php',
            'template' => 'legal',
            'seo_title' => 'Photo Credits | {business}',
            'seo_description' => 'Credits and licences of the car photos used on the {business} website.',
            'content' => [
                'page_name' => 'Photo Credits',
                'hero_title' => 'Photo Credits',
                'cta_show' => false,
                'body' => file_get_contents(__DIR__.'/photo-credits.html'),
            ],
        ],

        /* ------------------------------------------------------------ SERVICE PAGES (footer "Services" links) */
        ...require __DIR__.'/service_pages.php',

        /* ------------------------------------------------------------ THANK YOU */
        [
            'title' => 'Thank You',
            'slug' => 'thank-you',
            'template' => 'thank-you',
            'seo_title' => 'Thank You | {business}',
            'seo_description' => 'Thank you for booking with {business}.',
            'content' => [
                'page_name' => 'Thank You',
                'heading_name' => 'Thank You, {name}!',
                'heading_general' => 'Thank You!',
                'message_item' => 'We received your booking request for {item}. Our team will call you shortly to confirm the fare and pickup time.',
                'message_general' => 'We received your booking request. Our team will call you shortly to confirm the fare and pickup time.',
                'small_line' => 'Need a cab right now? Call or WhatsApp us directly.',
            ],
        ],
    ],

    /* ---------------------------------------------------------------- BLOG POSTS (article + FAQ + cab cards) */
    'posts' => array_map(fn ($p) => $p + ['content' => [
        'page_name' => $p['title'],
        'hero_title' => $p['h1'],
        'hero_text' => '',
        'cta_show' => true,
        'blocks' => [
            $b('rich_text', ['small' => '', 'heading' => '', 'body' => file_get_contents(__DIR__.'/posts/'.$p['body']), 'background' => 'white']),
            $b('faq', ['small' => 'FAQ', 'heading' => 'Frequently Asked Questions', 'text' => '', 'background' => 'white', 'side_call' => false, 'help_show' => false, 'items' => $p['faq']]),
            $postCabs(),
        ],
    ]], $posts),

    /* ---------------------------------------------------------------- CAB CARDS (fare on request) */
    'services' => [
        ['title' => 'Sedan', 'subtitle' => 'Swift Dzire · Toyota Etios or similar', 'label' => 'Budget', 'image' => 'images/cabs/photo-sedan.jpg', 'image_alt' => 'Swift Dzire sedan taxi in Dehradun',
            'features' => ['4+1 Seats', '2 Bags', 'AC'], 'text' => 'Best for: airport, station & city rides',
            'price_mode' => 'request'],
        ['title' => 'SUV', 'subtitle' => 'Maruti Ertiga or similar', 'label' => 'Family', 'image' => 'images/cabs/photo-suv.jpg', 'image_alt' => 'Ertiga SUV cab for family trips from Dehradun',
            'features' => ['6+1 Seats', '3 Bags', 'AC'], 'text' => 'Best for: family trips to Mussoorie & Rishikesh',
            'price_mode' => 'request'],
        ['title' => 'Toyota Innova', 'subtitle' => 'Innova or similar MUV', 'badge' => 'Most Popular', 'label' => 'Comfort', 'image' => 'images/cabs/photo-innova.jpg', 'image_alt' => 'Toyota Innova taxi in Dehradun',
            'features' => ['7+1 Seats', '4 Bags', 'AC'], 'text' => 'Best for: hill tours & Char Dham Yatra',
            'price_mode' => 'request'],
        ['title' => 'Innova Crysta', 'subtitle' => 'Toyota Innova Crysta', 'badge' => 'Premium', 'label' => 'Premium', 'image' => 'images/cabs/photo-crysta.jpg', 'image_alt' => 'Toyota Innova Crysta cab in Dehradun',
            'features' => ['6–7 Seats', '4 Bags', 'AC'], 'text' => 'Best for: long trips in extra comfort',
            'price_mode' => 'request'],
        ['title' => 'Tempo Traveller', 'subtitle' => 'Force Traveller · group seating', 'label' => 'Group', 'image' => 'images/cabs/photo-traveller.jpg', 'image_alt' => 'Tempo Traveller on rent in Dehradun',
            'features' => ['12+ Seats', 'Luggage carrier', 'AC'], 'text' => 'Best for: group tours & yatras',
            'price_mode' => 'request'],
    ],
];
