<?php

return [

    'default' => [
        'role' => 'user',
        'tax' => 'my'
    ],

    'app' => [
        'title' => ['slug' => 'title', 'name' => 'Common Ground Works Sdn Bhd'],
        'description' => ['slug' => 'description', 'name' => "Common Ground, At the forefront of creating amazing office spaces throughout Southeast Asia, be part of a growing community that strives for success and gain access to a variety of business support and growth services."],
        'keywords' => ['slug' => 'keywords', 'name' => "co-working, coworking, shared office, virtual office, Service office, office, Malaysia, 
         Kuala lumpur, Damansara, bangsar, KL, petaling jaya, TTDI, community"]
    ],
    
    'company' => [
         'name' => 'Common Group',
         'address' => 'Penthouse 16-1 Level 16, Wisma UOA Damansara &#8545;, No 6, Changkat Semantan, Damansara Heights, 50490 Kuala Lumpur.'
    ],

    'mail' => [
        'sincere' => ['slug' => 'sincere', 'name' => 'The Community Team, Common Ground.'],
    ],

    'module' => [
        'admin' => ['slug' => '0', 'name' => 'Admin'],
        'member' => ['slug' => '1', 'name' => 'Member'],
        'company' => ['slug' => '2', 'name' => 'Company'],
        'agent' => ['slug' => '3', 'name' => 'Agent']
    ],

    'role' => [
        'root' => ['slug' => 'root', 'name' => 'Root'],
        'super-admin' => ['slug' => 'super-admin', 'name' => 'Super Admin'],
        'admin' => ['slug' => 'admin', 'name' => 'Admin'],
        'agent' => ['slug' => 'agent', 'name' => 'Agent'],
        'finance' => ['slug' => 'finance', 'name' => 'Finance'],
		'manager' => ['slug' => 'manager', 'name' => 'Manager'],
	    'salesperson' => ['slug' => 'salesperson', 'name' => 'Salesperson'],
        'staff' => ['slug' => 'staff', 'name' => 'Staff'],
        'user' => ['slug' => 'user', 'name' => 'Member']
    ],

    
    'status' => [
        '0' => ['slug' => 0, 'name' => 'Inactive'],
        '1' => ['slug' => 1, 'name' => 'Active']
    ],

    'publish' => [
        '0' => ['slug' => 0, 'name' => 'Unpublished'],
        '1' => ['slug' => 1, 'name' => 'Published']
    ],

    'flag' => [
        '0' => ['slug' => 0, 'name' => 'No'],
        '1' => ['slug' => 1, 'name' => 'Yes']
    ],

    'switch' => [
        '0' => ['slug' => 0, 'name' => 'Off'],
        '1' => ['slug' => 1, 'name' => 'On']
    ],

    'gender' => [
        'male' => ['slug' => 'male', 'name' => 'Male'],
        'female' => ['slug' => 'female', 'name' => 'Female']
    ],

    'salutation' => [
        'dr' => ['slug' => 'dr', 'name' => 'Dr'],
        'miss' => ['slug' => 'miss', 'name' => 'Miss'],
        'mr' => ['slug' => 'mr', 'name' => 'Mr'],
        'mrs' => ['slug' => 'mrs', 'name' => 'Mrs'],
        'ms' => ['slug' => 'ms', 'name' => 'Ms'],
        'prof' => ['slug' => 'prof', 'name' => 'Prof']
    ],

    'country' => [
        'malaysia' => [
            'slug' => 'malaysia',
            'name' => 'MALAYSIA',
            'city' => [
                'kuala-lumpur' => [
                    'slug' => 'kuala-lumpur',
                    'name' => 'KUALA LUMPUR',
                    'place' => [
                        'wisma-uoa-damansara-2' => ['slug' => 'wisma-uoa-damansara-2','name' => 'WISMA UOA DAMANSARA II'],
                        'wisma-mont-kiara' => ['slug' => 'wisma-mont-kiara', 'name' => "WISMA MONT' KIARA"],
                        'menara-ken-ttdi' => ['slug' => 'menara-ken-ttdi', 'name' => "MENARA KEN TTDI"],
                        'bukit-bintang' => ['slug' => "bukit-bintang", 'name' => "Bukit Bintang"],
                        'ara-damansara' => ['slug' => "ara-damansara", 'name' => "Ara Damansara"],
                        'ampang' => ['slug' => 'ampang', 'name' => 'AMPANG'],
                        'subang' => ['slug' => 'subang', 'name' => 'SUBANG'],
                    ]
                ]

            ]
        ],
    ],

    'skills' => array ( 'accounting' => array ( 'slug' => 'accounting', 'name' => 'Accounting', ), 'adobe photoshop' => array ( 'slug' => 'adobe photoshop', 'name' => 'Adobe Photoshop', ), 'adobe premier pro' => array ( 'slug' => 'adobe premier pro', 'name' => 'Adobe Premier Pro', ), 'after effects' => array ( 'slug' => 'after effects', 'name' => 'After Effects', ), 'asset management' => array ( 'slug' => 'asset management', 'name' => 'Asset Management', ), 'b2b' => array ( 'slug' => 'b2b', 'name' => 'B2B', ), 'big data analytics' => array ( 'slug' => 'big data analytics', 'name' => 'Big Data Analytics', ), 'branding & identity' => array ( 'slug' => 'branding & identity', 'name' => 'Branding & Identity', ), 'business development' => array ( 'slug' => 'business development', 'name' => 'Business Development', ), 'business intelligence' => array ( 'slug' => 'business intelligence', 'name' => 'Business Intelligence', ), 'business planning' => array ( 'slug' => 'business planning', 'name' => 'Business Planning', ), 'business strategy' => array ( 'slug' => 'business strategy', 'name' => 'Business Strategy', ), 'camera operation' => array ( 'slug' => 'camera operation', 'name' => 'Camera Operation', ), 'capital raising' => array ( 'slug' => 'capital raising', 'name' => 'Capital Raising', ), 'change managemt' => array ( 'slug' => 'change managemt', 'name' => 'Change Managemt', ), 'cold calling' => array ( 'slug' => 'cold calling', 'name' => 'Cold Calling', ), 'college savings plans' => array ( 'slug' => 'college savings plans', 'name' => 'College Savings Plans', ), 'commercial real estate' => array ( 'slug' => 'commercial real estate', 'name' => 'Commercial Real estate', ), 'communication' => array ( 'slug' => 'communication', 'name' => 'Communication', ), 'contract negotiation' => array ( 'slug' => 'contract negotiation', 'name' => 'Contract Negotiation', ), 'copywriting' => array ( 'slug' => 'copywriting', 'name' => 'Copywriting', ), 'corporate events' => array ( 'slug' => 'corporate events', 'name' => 'Corporate Events', ), 'corporate governance' => array ( 'slug' => 'corporate governance', 'name' => 'Corporate Governance', ), 'corporate real transactions' => array ( 'slug' => 'corporate real transactions', 'name' => 'Corporate Real Transactions', ), 'critical illness' => array ( 'slug' => 'critical illness', 'name' => 'Critical Illness', ), 'crm' => array ( 'slug' => 'crm', 'name' => 'CRM', ), 'cross- functional' => array ( 'slug' => 'cross-functional', 'name' => 'Cross-Functional', ), 'customer service' => array ( 'slug' => 'customer service', 'name' => 'Customer Service', ), 'customer services' => array ( 'slug' => 'customer services', 'name' => 'Customer Services', ), 'customer servise' => array ( 'slug' => 'customer servise', 'name' => 'Customer Servise', ), 'data analytics' => array ( 'slug' => 'data analytics', 'name' => 'Data Analytics', ), 'digital marketing' => array ( 'slug' => 'digital marketing', 'name' => 'Digital Marketing', ), 'digital media' => array ( 'slug' => 'digital media', 'name' => 'Digital media', ), 'digital photography' => array ( 'slug' => 'digital photography', 'name' => 'Digital Photography', ), 'disability insurance' => array ( 'slug' => 'disability insurance', 'name' => 'Disability Insurance', ), 'disposition' => array ( 'slug' => 'disposition', 'name' => 'Disposition', ), 'english' => array ( 'slug' => 'english', 'name' => 'English', ), 'entertainment' => array ( 'slug' => 'entertainment', 'name' => 'Entertainment', ), 'entrepreneurship' => array ( 'slug' => 'entrepreneurship', 'name' => 'Entrepreneurship', ), 'event management' => array ( 'slug' => 'event management', 'name' => 'Event Management', ), 'event planning' => array ( 'slug' => 'event planning', 'name' => 'Event Planning', ), 'executive management' => array ( 'slug' => 'executive management', 'name' => 'Executive Management', ), 'external audit' => array ( 'slug' => 'external audit', 'name' => 'External Audit', ), 'film' => array ( 'slug' => 'film', 'name' => 'Film', ), 'financial accounting' => array ( 'slug' => 'financial accounting', 'name' => 'Financial Accounting', ), 'financial analysis' => array ( 'slug' => 'financial analysis', 'name' => 'Financial Analysis', ), 'financial audits' => array ( 'slug' => 'financial audits', 'name' => 'Financial Audits', ), 'financial modeling' => array ( 'slug' => 'financial modeling', 'name' => 'Financial Modeling', ), 'financial planning' => array ( 'slug' => 'financial planning', 'name' => 'Financial Planning', ), 'financial reporting' => array ( 'slug' => 'financial reporting', 'name' => 'Financial Reporting', ), 'financial services' => array ( 'slug' => 'financial services', 'name' => 'Financial Services', ), 'go to market strategy' => array ( 'slug' => 'go to market strategy', 'name' => 'Go To Market Strategy', ), 'hospitality' => array ( 'slug' => 'hospitality', 'name' => 'Hospitality', ), 'ifrs' => array ( 'slug' => 'ifrs', 'name' => 'IFRS', ), 'illustrator' => array ( 'slug' => 'illustrator', 'name' => 'Illustrator', ), 'insurance' => array ( 'slug' => 'insurance', 'name' => 'Insurance', ), 'integration' => array ( 'slug' => 'integration', 'name' => 'Integration', ), 'internal audit' => array ( 'slug' => 'internal audit', 'name' => 'Internal Audit', ), 'internal controls' => array ( 'slug' => 'internal controls', 'name' => 'Internal Controls', ), 'international financier' => array ( 'slug' => 'international financier', 'name' => 'International Financier', ), 'international sales' => array ( 'slug' => 'international sales', 'name' => 'International Sales', ), 'international tax' => array ( 'slug' => 'international tax', 'name' => 'International Tax', ), 'investment management' => array ( 'slug' => 'investment management', 'name' => 'Investment Management', ), 'investments' => array ( 'slug' => 'investments', 'name' => 'Investments', ), 'landlord' => array ( 'slug' => 'landlord', 'name' => 'Landlord', ), 'lead generation' => array ( 'slug' => 'lead generation', 'name' => 'Lead Generation', ), 'leadership' => array ( 'slug' => 'leadership', 'name' => 'Leadership', ), 'life insurance' => array ( 'slug' => 'life insurance', 'name' => 'Life Insurance', ), 'long term care insurance' => array ( 'slug' => 'long term care insurance', 'name' => 'Long Term Care Insurance', ), 'management' => array ( 'slug' => 'management', 'name' => 'Management', ), 'management consulting' => array ( 'slug' => 'management consulting', 'name' => 'Management Consulting', ), 'market analysis' => array ( 'slug' => 'market analysis', 'name' => 'Market Analysis', ), 'market research' => array ( 'slug' => 'market research', 'name' => 'Market Research', ), 'marketing' => array ( 'slug' => 'marketing', 'name' => 'Marketing', ), 'marketing communication' => array ( 'slug' => 'marketing communication', 'name' => 'Marketing Communication', ), 'marketing strategy' => array ( 'slug' => 'marketing strategy', 'name' => 'Marketing Strategy', ), 'microsoft office' => array ( 'slug' => 'microsoft office', 'name' => 'Microsoft Office', ), 'microsoft word' => array ( 'slug' => 'microsoft word', 'name' => 'Microsoft Word', ), 'mutual funds' => array ( 'slug' => 'mutual funds', 'name' => 'Mutual Funds', ), 'negotiation' => array ( 'slug' => 'negotiation', 'name' => 'Negotiation', ), 'ninja skills' => array ( 'slug' => 'ninja skills', 'name' => 'Ninja Skills', ), 'photography' => array ( 'slug' => 'photography', 'name' => 'Photography', ), 'photojournalism' => array ( 'slug' => 'photojournalism', 'name' => 'Photojournalism', ), 'photoshop' => array ( 'slug' => 'photoshop', 'name' => 'Photoshop', ), 'private equity' => array ( 'slug' => 'private equity', 'name' => 'Private Equity', ), 'product marketing' => array ( 'slug' => 'product marketing', 'name' => 'Product Marketing', ), 'program management' => array ( 'slug' => 'program management', 'name' => 'Program Management', ), 'project management' => array ( 'slug' => 'project management', 'name' => 'Project Management', ), 'public relations' => array ( 'slug' => 'public relations', 'name' => 'Public Relations', ), 'real estate' => array ( 'slug' => 'real estate', 'name' => 'Real Estate', ), 'real estate development' => array ( 'slug' => 'real estate development', 'name' => 'Real Estate Development', ), 'real estate economy' => array ( 'slug' => 'real estate economy', 'name' => 'Real Estate Economy', ), 'real estate transactions' => array ( 'slug' => 'real estate transactions', 'name' => 'Real Estate Transactions', ), 'report writing' => array ( 'slug' => 'report writing', 'name' => 'Report Writing', ), 'research' => array ( 'slug' => 'research', 'name' => 'Research', ), 'retirement' => array ( 'slug' => 'retirement', 'name' => 'Retirement', ), 'retirement planning' => array ( 'slug' => 'retirement planning', 'name' => 'Retirement planning', ), 'retirement savins' => array ( 'slug' => 'retirement savins', 'name' => 'Retirement Savins', ), 'sales' => array ( 'slug' => 'sales', 'name' => 'Sales', ), 'sales operations' => array ( 'slug' => 'sales operations', 'name' => 'Sales Operations', ), 'saving for education' => array ( 'slug' => 'saving for education', 'name' => 'Saving for Education', ), 'social media' => array ( 'slug' => 'social media', 'name' => 'Social Media', ), 'social media marketing' => array ( 'slug' => 'social media marketing', 'name' => 'Social Media Marketing', ), 'social networking' => array ( 'slug' => 'social networking', 'name' => 'Social Networking', ), 'start-ups' => array ( 'slug' => 'start-ups', 'name' => 'Start-Ups', ), 'strategic financial planning' => array ( 'slug' => 'strategic financial planning', 'name' => 'Strategic Financial planning', ), 'strategic planning' => array ( 'slug' => 'strategic planning', 'name' => 'Strategic Planning', ), 'strategy' => array ( 'slug' => 'strategy', 'name' => 'Strategy', ), 'software engineer' => array ( 'slug' => 'software engineer', 'name' => 'Software Engineer', ), 'tax accounting' => array ( 'slug' => 'tax accounting', 'name' => 'Tax Accounting', ), 'tax law' => array ( 'slug' => 'tax law', 'name' => 'Tax Law', ), 'teaching' => array ( 'slug' => 'teaching', 'name' => 'Teaching', ), 'team leadership' => array ( 'slug' => 'team leadership', 'name' => 'Team Leadership', ), 'teamwork' => array ( 'slug' => 'teamwork', 'name' => 'Teamwork', ), 'telecommunication' => array ( 'slug' => 'telecommunication', 'name' => 'Telecommunication', ), 'television' => array ( 'slug' => 'television', 'name' => 'Television', ), 'terminal illness' => array ( 'slug' => 'terminal illness', 'name' => 'Terminal illness', ), 'tourism' => array ( 'slug' => 'tourism', 'name' => 'Tourism', ), 'trade marketing' => array ( 'slug' => 'trade marketing', 'name' => 'Trade Marketing', ), 'training' => array ( 'slug' => 'training', 'name' => 'Training', ), 'valuation' => array ( 'slug' => 'valuation', 'name' => 'Valuation', ), 'variable life' => array ( 'slug' => 'variable life', 'name' => 'Variable Life', ), 'vendor management' => array ( 'slug' => 'vendor management', 'name' => 'Vendor Management', ), 'video production' => array ( 'slug' => 'video production', 'name' => 'Video Production', ), 'wealth management' => array ( 'slug' => 'wealth management', 'name' => 'Wealth Management', ), 'windows' => array ( 'slug' => 'windows', 'name' => 'Windows', ), 'wireless' => array ( 'slug' => 'wireless', 'name' => 'Wireless' )),


    'interests' => array ( '3d printing' => array ( 'slug' => '3d printing', 'name' => '3D Printing', ), 'acting' => array ( 'slug' => 'acting', 'name' => 'Acting', ), 'action' => array ( 'slug' => 'action', 'name' => 'Action', ), 'action figure' => array ( 'slug' => 'action figure', 'name' => 'Action Figure', ), 'actors / actresses' => array ( 'slug' => 'actors / actresses', 'name' => 'Actors / Actresses', ), 'adoption' => array ( 'slug' => 'adoption', 'name' => 'Adoption', ), 'adventure' => array ( 'slug' => 'adventure', 'name' => 'Adventure', ), 'afghanistan' => array ( 'slug' => 'afghanistan', 'name' => 'Afghanistan', ), 'african-american' => array ( 'slug' => 'african-american', 'name' => 'African-American', ), 'air sports' => array ( 'slug' => 'air sports', 'name' => 'Air Sports', ), 'aircraft spotting' => array ( 'slug' => 'aircraft spotting', 'name' => 'Aircraft Spotting', ), 'airsoft' => array ( 'slug' => 'airsoft', 'name' => 'Airsoft', ), 'alaska' => array ( 'slug' => 'alaska', 'name' => 'Alaska', ), 'alternative schooling' => array ( 'slug' => 'alternative schooling', 'name' => 'Alternative Schooling', ), 'amateur astronomy' => array ( 'slug' => 'amateur astronomy', 'name' => 'Amateur Astronomy', ), 'amateur radio' => array ( 'slug' => 'amateur radio', 'name' => 'Amateur Radio', ), 'american football' => array ( 'slug' => 'american football', 'name' => 'American Football', ), 'american history' => array ( 'slug' => 'american history', 'name' => 'American History', ), 'americana' => array ( 'slug' => 'americana', 'name' => 'Americana', ), 'animal care' => array ( 'slug' => 'animal care', 'name' => 'Animal Care', ), 'animal stories' => array ( 'slug' => 'animal stories', 'name' => 'Animal Stories', ), 'animals' => array ( 'slug' => 'animals', 'name' => 'Animals', ), 'antiquing' => array ( 'slug' => 'antiquing', 'name' => 'Antiquing', ), 'antiquities' => array ( 'slug' => 'antiquities', 'name' => 'Antiquities', ), 'apocolypse' => array ( 'slug' => 'apocolypse', 'name' => 'Apocolypse', ), 'archery' => array ( 'slug' => 'archery', 'name' => 'Archery', ), 'art' => array ( 'slug' => 'art', 'name' => 'Art', ), 'art collecting' => array ( 'slug' => 'art collecting', 'name' => 'Art Collecting', ), 'asia' => array ( 'slug' => 'asia', 'name' => 'Asia', ), 'association football' => array ( 'slug' => 'association football', 'name' => 'Association Football', ), 'astrology' => array ( 'slug' => 'astrology', 'name' => 'Astrology', ), 'astronomy' => array ( 'slug' => 'astronomy', 'name' => 'Astronomy', ), 'australian rules football' => array ( 'slug' => 'australian rules football', 'name' => 'Australian Rules Football', ), 'auto audiophilia' => array ( 'slug' => 'auto audiophilia', 'name' => 'Auto Audiophilia', ), 'auto racing' => array ( 'slug' => 'auto racing', 'name' => 'Auto Racing', ), 'autobiography' => array ( 'slug' => 'autobiography', 'name' => 'Autobiography', ), 'base jumping' => array ( 'slug' => 'base jumping', 'name' => 'BASE Jumping', ), 'baseball' => array ( 'slug' => 'baseball', 'name' => 'Baseball', ), 'basketball' => array ( 'slug' => 'basketball', 'name' => 'Basketball', ), 'baton twirling' => array ( 'slug' => 'baton twirling', 'name' => 'Baton Twirling', ), 'beach volleyball' => array ( 'slug' => 'beach volleyball', 'name' => 'Beach Volleyball', ), 'beekeeping' => array ( 'slug' => 'beekeeping', 'name' => 'Beekeeping', ), 'bird watching' => array ( 'slug' => 'bird watching', 'name' => 'Bird Watching', ), 'blacksmithing' => array ( 'slug' => 'blacksmithing', 'name' => 'Blacksmithing', ), 'blogging' => array ( 'slug' => 'blogging', 'name' => 'Blogging', ), 'board games' => array ( 'slug' => 'board games', 'name' => 'Board Games', ), 'board sports' => array ( 'slug' => 'board sports', 'name' => 'Board Sports', ), 'bodybuilding' => array ( 'slug' => 'bodybuilding', 'name' => 'Bodybuilding', ), 'book collecting' => array ( 'slug' => 'book collecting', 'name' => 'Book Collecting', ), 'book restoration' => array ( 'slug' => 'book restoration', 'name' => 'Book Restoration', ), 'books' => array ( 'slug' => 'books', 'name' => 'Books', ), 'brazilian jiu-jitsu' => array ( 'slug' => 'brazilian jiu-jitsu', 'name' => 'Brazilian Jiu-Jitsu', ), 'breakdancing' => array ( 'slug' => 'breakdancing', 'name' => 'Breakdancing', ), 'bus spotting' => array ( 'slug' => 'bus spotting', 'name' => 'Bus Spotting', ), 'cabaret' => array ( 'slug' => 'cabaret', 'name' => 'Cabaret', ), 'calligraphy' => array ( 'slug' => 'calligraphy', 'name' => 'Calligraphy', ), 'camping' => array ( 'slug' => 'camping', 'name' => 'Camping', ), 'candle making' => array ( 'slug' => 'candle making', 'name' => 'Candle Making', ), 'canyoning' => array ( 'slug' => 'canyoning', 'name' => 'Canyoning', ), 'card collecting' => array ( 'slug' => 'card collecting', 'name' => 'Card Collecting', ), 'child care' => array ( 'slug' => 'child care', 'name' => 'Child Care', ), 'climbing' => array ( 'slug' => 'climbing', 'name' => 'Climbing', ), 'club membership' => array ( 'slug' => 'club membership', 'name' => 'Club Membership', ), 'coffee roasting' => array ( 'slug' => 'coffee roasting', 'name' => 'Coffee Roasting', ), 'coin collecting' => array ( 'slug' => 'coin collecting', 'name' => 'Coin Collecting', ), 'collecting' => array ( 'slug' => 'collecting', 'name' => 'Collecting', ), 'coloring' => array ( 'slug' => 'coloring', 'name' => 'Coloring', ), 'comedy' => array ( 'slug' => 'comedy', 'name' => 'Comedy', ), 'comic book collecting' => array ( 'slug' => 'comic book collecting', 'name' => 'Comic Book Collecting', ), 'computer' => array ( 'slug' => 'computer', 'name' => 'Computer', ), 'computer programming' => array ( 'slug' => 'computer programming', 'name' => 'Computer Programming', ), 'cooking' => array ( 'slug' => 'cooking', 'name' => 'Cooking', ), 'cosplaying' => array ( 'slug' => 'cosplaying', 'name' => 'Cosplaying', ), 'couponing' => array ( 'slug' => 'couponing', 'name' => 'Couponing', ), 'creative writing' => array ( 'slug' => 'creative writing', 'name' => 'Creative Writing', ), 'cricket' => array ( 'slug' => 'cricket', 'name' => 'Cricket', ), 'crocheting' => array ( 'slug' => 'crocheting', 'name' => 'Crocheting', ), 'cross-stitch' => array ( 'slug' => 'cross-stitch', 'name' => 'Cross-stitch', ), 'crossword puzzles' => array ( 'slug' => 'crossword puzzles', 'name' => 'Crossword Puzzles', ), 'cryptography' => array ( 'slug' => 'cryptography', 'name' => 'Cryptography', ), 'cycling' => array ( 'slug' => 'cycling', 'name' => 'Cycling', ), 'dance' => array ( 'slug' => 'dance', 'name' => 'Dance', ), 'dancing' => array ( 'slug' => 'dancing', 'name' => 'Dancing', ), 'deltiology (postcard collecting)' => array ( 'slug' => 'deltiology (postcard collecting)', 'name' => 'Deltiology (Postcard Collecting)', ), 'die-cast toy' => array ( 'slug' => 'die-cast toy', 'name' => 'Die-Cast Toy', ), 'digital arts' => array ( 'slug' => 'digital arts', 'name' => 'Digital Arts', ), 'disc golf' => array ( 'slug' => 'disc golf', 'name' => 'Disc Golf', ), 'do it yourself' => array ( 'slug' => 'do it yourself', 'name' => 'Do It Yourself', ), 'dog sport' => array ( 'slug' => 'dog sport', 'name' => 'Dog Sport', ), 'dowsing' => array ( 'slug' => 'dowsing', 'name' => 'Dowsing', ), 'drama' => array ( 'slug' => 'drama', 'name' => 'Drama', ), 'drawing' => array ( 'slug' => 'drawing', 'name' => 'Drawing', ), 'driving' => array ( 'slug' => 'driving', 'name' => 'Driving', ), 'electronics' => array ( 'slug' => 'electronics', 'name' => 'Electronics', ), 'element collecting' => array ( 'slug' => 'element collecting', 'name' => 'Element Collecting', ), 'embroidery' => array ( 'slug' => 'embroidery', 'name' => 'Embroidery', ), 'equestrianism' => array ( 'slug' => 'equestrianism', 'name' => 'Equestrianism', ), 'exhibition drill' => array ( 'slug' => 'exhibition drill', 'name' => 'Exhibition Drill', ), 'fantasy sports' => array ( 'slug' => 'fantasy sports', 'name' => 'Fantasy Sports', ), 'fashion' => array ( 'slug' => 'fashion', 'name' => 'fashion', ), 'field hockey' => array ( 'slug' => 'field hockey', 'name' => 'Field Hockey', ), 'figure skating' => array ( 'slug' => 'figure skating', 'name' => 'Figure Skating', ), 'fishing' => array ( 'slug' => 'fishing', 'name' => 'Fishing', ), 'fishkeeping' => array ( 'slug' => 'fishkeeping', 'name' => 'Fishkeeping', ), 'flag football' => array ( 'slug' => 'flag football', 'name' => 'Flag Football', ), 'flower arranging' => array ( 'slug' => 'flower arranging', 'name' => 'Flower Arranging', ), 'flower collecting and pressing' => array ( 'slug' => 'flower collecting and pressing', 'name' => 'Flower Collecting and Pressing', ), 'flying' => array ( 'slug' => 'flying', 'name' => 'Flying', ), 'flying disc' => array ( 'slug' => 'flying disc', 'name' => 'Flying Disc', ), 'footbag' => array ( 'slug' => 'footbag', 'name' => 'Footbag', ), 'football' => array ( 'slug' => 'football', 'name' => 'Football', ), 'foraging' => array ( 'slug' => 'foraging', 'name' => 'Foraging', ), 'foreign language learning' => array ( 'slug' => 'foreign language learning', 'name' => 'Foreign Language Learning', ), 'fossil hunting' => array ( 'slug' => 'fossil hunting', 'name' => 'Fossil Hunting', ), 'freestyle football' => array ( 'slug' => 'freestyle football', 'name' => 'Freestyle Football', ), 'gaming' => array ( 'slug' => 'gaming', 'name' => 'Gaming', ), 'gaming (tabletop games and role-playing games)' => array ( 'slug' => 'gaming (tabletop games and role-playing games)', 'name' => 'Gaming (Tabletop Games and Role-Playing Games)', ), 'gardening' => array ( 'slug' => 'gardening', 'name' => 'Gardening', ), 'genealogy' => array ( 'slug' => 'genealogy', 'name' => 'Genealogy', ), 'geocaching' => array ( 'slug' => 'geocaching', 'name' => 'Geocaching', ), 'ghost hunting' => array ( 'slug' => 'ghost hunting', 'name' => 'Ghost Hunting', ), 'glassblowing' => array ( 'slug' => 'glassblowing', 'name' => 'Glassblowing', ), 'golfing' => array ( 'slug' => 'golfing', 'name' => 'Golfing', ), 'gongoozling' => array ( 'slug' => 'gongoozling', 'name' => 'Gongoozling', ), 'graffiti' => array ( 'slug' => 'graffiti', 'name' => 'Graffiti', ), 'gunsmithing' => array ( 'slug' => 'gunsmithing', 'name' => 'Gunsmithing', ), 'handball' => array ( 'slug' => 'handball', 'name' => 'Handball', ), 'herping' => array ( 'slug' => 'herping', 'name' => 'Herping', ), 'high-power rocketry' => array ( 'slug' => 'high-power rocketry', 'name' => 'High-Power Rocketry', ), 'hiking' => array ( 'slug' => 'hiking', 'name' => 'Hiking', ), 'hiking / backpacking' => array ( 'slug' => 'hiking / backpacking', 'name' => 'Hiking / Backpacking', ), 'homebrewing' => array ( 'slug' => 'homebrewing', 'name' => 'Homebrewing', ), 'hooping' => array ( 'slug' => 'hooping', 'name' => 'Hooping', ), 'horseback riding' => array ( 'slug' => 'horseback riding', 'name' => 'Horseback Riding', ), 'hunting' => array ( 'slug' => 'hunting', 'name' => 'Hunting', ), 'hydroponics' => array ( 'slug' => 'hydroponics', 'name' => 'Hydroponics', ), 'ice hockey' => array ( 'slug' => 'ice hockey', 'name' => 'Ice Hockey', ), 'ice skating' => array ( 'slug' => 'ice skating', 'name' => 'Ice Skating', ), 'inline skating' => array ( 'slug' => 'inline skating', 'name' => 'Inline Skating', ), 'insect collecting' => array ( 'slug' => 'insect collecting', 'name' => 'Insect Collecting', ), 'jewelry making' => array ( 'slug' => 'jewelry making', 'name' => 'Jewelry Making', ), 'jigsaw puzzles' => array ( 'slug' => 'jigsaw puzzles', 'name' => 'Jigsaw Puzzles', ), 'jogging' => array ( 'slug' => 'jogging', 'name' => 'Jogging', ), 'judo' => array ( 'slug' => 'judo', 'name' => 'Judo', ), 'juggling' => array ( 'slug' => 'juggling', 'name' => 'Juggling', ), 'jukskei' => array ( 'slug' => 'jukskei', 'name' => 'Jukskei', ), 'kart racing' => array ( 'slug' => 'kart racing', 'name' => 'Kart Racing', ), 'kayaking' => array ( 'slug' => 'kayaking', 'name' => 'Kayaking', ), 'kite flying' => array ( 'slug' => 'kite flying', 'name' => 'Kite Flying', ), 'kitesurfing' => array ( 'slug' => 'kitesurfing', 'name' => 'Kitesurfing', ), 'knapping' => array ( 'slug' => 'knapping', 'name' => 'Knapping', ), 'knife making' => array ( 'slug' => 'knife making', 'name' => 'Knife Making', ), 'knife throwing' => array ( 'slug' => 'knife throwing', 'name' => 'Knife Throwing', ), 'knitting' => array ( 'slug' => 'knitting', 'name' => 'Knitting', ), 'kombucha brewing' => array ( 'slug' => 'kombucha brewing', 'name' => 'Kombucha Brewing', ), 'lacemaking' => array ( 'slug' => 'lacemaking', 'name' => 'Lacemaking', ), 'lacrosse' => array ( 'slug' => 'lacrosse', 'name' => 'Lacrosse', ), 'lapidary' => array ( 'slug' => 'lapidary', 'name' => 'Lapidary', ), 'larping' => array ( 'slug' => 'larping', 'name' => 'Larping', ), 'learning' => array ( 'slug' => 'learning', 'name' => 'Learning', ), 'leather crafting' => array ( 'slug' => 'leather crafting', 'name' => 'Leather Crafting', ), 'lego building' => array ( 'slug' => 'lego building', 'name' => 'Lego Building', ), 'letterboxing' => array ( 'slug' => 'letterboxing', 'name' => 'Letterboxing', ), 'listening to music' => array ( 'slug' => 'listening to music', 'name' => 'Listening to Music', ), 'machining' => array ( 'slug' => 'machining', 'name' => 'Machining', ), 'macrame' => array ( 'slug' => 'macrame', 'name' => 'Macrame', ), 'magic' => array ( 'slug' => 'magic', 'name' => 'Magic', ), 'marching band' => array ( 'slug' => 'marching band', 'name' => 'Marching Band', ), 'metal detecting' => array ( 'slug' => 'metal detecting', 'name' => 'Metal Detecting', ), 'metalworking' => array ( 'slug' => 'metalworking', 'name' => 'Metalworking', ), 'meteorology' => array ( 'slug' => 'meteorology', 'name' => 'Meteorology', ), 'microscopy' => array ( 'slug' => 'microscopy', 'name' => 'Microscopy', ), 'mineral collecting' => array ( 'slug' => 'mineral collecting', 'name' => 'Mineral Collecting', ), 'model aircraft' => array ( 'slug' => 'model aircraft', 'name' => 'Model Aircraft', ), 'model building' => array ( 'slug' => 'model building', 'name' => 'Model Building', ), 'motor sports' => array ( 'slug' => 'motor sports', 'name' => 'Motor Sports', ), 'mountain biking' => array ( 'slug' => 'mountain biking', 'name' => 'Mountain Biking', ), 'mountaineering' => array ( 'slug' => 'mountaineering', 'name' => 'Mountaineering', ), 'movie and movie memorabilia collecting' => array ( 'slug' => 'movie and movie memorabilia collecting', 'name' => 'Movie and Movie Memorabilia Collecting', ), 'movies' => array ( 'slug' => 'movies', 'name' => 'Movies', ), 'mushroom hunting / mycology' => array ( 'slug' => 'mushroom hunting / mycology', 'name' => 'Mushroom Hunting / Mycology', ), 'music' => array ( 'slug' => 'music', 'name' => 'Music', ), 'netball' => array ( 'slug' => 'netball', 'name' => 'Netball', ), 'nordic skating' => array ( 'slug' => 'nordic skating', 'name' => 'Nordic Skating', ), 'orienteering' => array ( 'slug' => 'orienteering', 'name' => 'Orienteering', ), 'origami' => array ( 'slug' => 'origami', 'name' => 'Origami', ), 'paintball' => array ( 'slug' => 'paintball', 'name' => 'Paintball', ), 'painting' => array ( 'slug' => 'painting', 'name' => 'Painting', ), 'parkour' => array ( 'slug' => 'parkour', 'name' => 'Parkour', ), 'pet' => array ( 'slug' => 'pet', 'name' => 'Pet', ), 'philately' => array ( 'slug' => 'philately', 'name' => 'Philately', ), 'photograph' => array ( 'slug' => 'photograph', 'name' => 'photograph', ), 'photography' => array ( 'slug' => 'photography', 'name' => 'Photography', ), 'plastic embedding' => array ( 'slug' => 'plastic embedding', 'name' => 'Plastic Embedding', ), 'playing musical instruments' => array ( 'slug' => 'playing musical instruments', 'name' => 'Playing Musical Instruments', ), 'poi' => array ( 'slug' => 'poi', 'name' => 'Poi', ), 'polo' => array ( 'slug' => 'polo', 'name' => 'Polo', ), 'pottery' => array ( 'slug' => 'pottery', 'name' => 'Pottery', ), 'powerlifting' => array ( 'slug' => 'powerlifting', 'name' => 'Powerlifting', ), 'puzzle' => array ( 'slug' => 'puzzle', 'name' => 'Puzzle', ), 'quilling' => array ( 'slug' => 'quilling', 'name' => 'Quilling', ), 'quilting' => array ( 'slug' => 'quilting', 'name' => 'Quilting', ), 'racquetball' => array ( 'slug' => 'racquetball', 'name' => 'Racquetball', ), 'radio-controlled car racing' => array ( 'slug' => 'radio-controlled car racing', 'name' => 'Radio-Controlled Car Racing', ), 'rafting' => array ( 'slug' => 'rafting', 'name' => 'Rafting', ), 'rappelling' => array ( 'slug' => 'rappelling', 'name' => 'Rappelling', ), 'reading' => array ( 'slug' => 'reading', 'name' => 'Reading', ), 'record collecting' => array ( 'slug' => 'record collecting', 'name' => 'Record Collecting', ), 'road biking' => array ( 'slug' => 'road biking', 'name' => 'Road Biking', ), 'rock balancing' => array ( 'slug' => 'rock balancing', 'name' => 'Rock Balancing', ), 'rock climbing' => array ( 'slug' => 'rock climbing', 'name' => 'Rock Climbing', ), 'roller derby' => array ( 'slug' => 'roller derby', 'name' => 'Roller Derby', ), 'roller skating' => array ( 'slug' => 'roller skating', 'name' => 'Roller Skating', ), 'rugby' => array ( 'slug' => 'rugby', 'name' => 'Rugby', ), 'rugby league football' => array ( 'slug' => 'rugby league football', 'name' => 'Rugby League Football', ), 'running' => array ( 'slug' => 'running', 'name' => 'Running', ), 'sailing' => array ( 'slug' => 'sailing', 'name' => 'Sailing', ), 'sand art' => array ( 'slug' => 'sand art', 'name' => 'Sand art', ), 'satellite watching' => array ( 'slug' => 'satellite watching', 'name' => 'Satellite Watching', ), 'scouting' => array ( 'slug' => 'scouting', 'name' => 'Scouting', ), 'scrapbooking' => array ( 'slug' => 'scrapbooking', 'name' => 'Scrapbooking', ), 'scuba diving' => array ( 'slug' => 'scuba diving', 'name' => 'Scuba diving', ), 'sculling or rowing' => array ( 'slug' => 'sculling or rowing', 'name' => 'Sculling or Rowing', ), 'sculpting' => array ( 'slug' => 'sculpting', 'name' => 'Sculpting', ), 'sea glass collecting' => array ( 'slug' => 'sea glass collecting', 'name' => 'Sea Glass Collecting', ), 'seashell collecting' => array ( 'slug' => 'seashell collecting', 'name' => 'Seashell Collecting', ), 'sewin' => array ( 'slug' => 'sewin', 'name' => 'Sewin', ), 'shooting' => array ( 'slug' => 'shooting', 'name' => 'Shooting', ), 'shooting sport' => array ( 'slug' => 'shooting sport', 'name' => 'Shooting Sport', ), 'shopping' => array ( 'slug' => 'shopping', 'name' => 'Shopping', ), 'shortwave listening' => array ( 'slug' => 'shortwave listening', 'name' => 'Shortwave Listening', ), 'singing' => array ( 'slug' => 'singing', 'name' => 'Singing', ), 'skateboarding' => array ( 'slug' => 'skateboarding', 'name' => 'Skateboarding', ), 'sketching' => array ( 'slug' => 'sketching', 'name' => 'Sketching', ), 'skiing' => array ( 'slug' => 'skiing', 'name' => 'Skiing', ), 'skimboarding' => array ( 'slug' => 'skimboarding', 'name' => 'Skimboarding', ), 'skydiving' => array ( 'slug' => 'skydiving', 'name' => 'Skydiving', ), 'slacklining' => array ( 'slug' => 'slacklining', 'name' => 'Slacklining', ), 'snowboarding' => array ( 'slug' => 'snowboarding', 'name' => 'Snowboarding', ), 'soapmaking' => array ( 'slug' => 'soapmaking', 'name' => 'Soapmaking', ), 'soccer' => array ( 'slug' => 'soccer', 'name' => 'Soccer', ), 'socializing' => array ( 'slug' => 'socializing', 'name' => 'Socializing', ), 'speed skating' => array ( 'slug' => 'speed skating', 'name' => 'Speed Skating', ), 'sports' => array ( 'slug' => 'sports', 'name' => 'Sports', ), 'squash' => array ( 'slug' => 'squash', 'name' => 'Squash', ), 'stamp collecting' => array ( 'slug' => 'stamp collecting', 'name' => 'Stamp Collecting', ), 'stand-up comedy' => array ( 'slug' => 'stand-up comedy', 'name' => 'Stand-Up Comedy', ), 'stone collecting' => array ( 'slug' => 'stone collecting', 'name' => 'Stone Collecting', ), 'stone skipping' => array ( 'slug' => 'stone skipping', 'name' => 'Stone Skipping', ), 'surfing' => array ( 'slug' => 'surfing', 'name' => 'Surfing', ), 'swimming' => array ( 'slug' => 'swimming', 'name' => 'Swimming', ), 'table tennis' => array ( 'slug' => 'table tennis', 'name' => 'Table Tennis', ), 'taekwond' => array ( 'slug' => 'taekwond', 'name' => 'Taekwond', ), 'tai chi' => array ( 'slug' => 'tai chi', 'name' => 'Tai Chi', ), 'tatting' => array ( 'slug' => 'tatting', 'name' => 'Tatting', ), 'taxidermy' => array ( 'slug' => 'taxidermy', 'name' => 'Taxidermy', ), 'tennis' => array ( 'slug' => 'tennis', 'name' => 'Tennis', ), 'topiary' => array ( 'slug' => 'topiary', 'name' => 'Topiary', ), 'tour skating' => array ( 'slug' => 'tour skating', 'name' => 'Tour Skating', ), 'trainspotting' => array ( 'slug' => 'trainspotting', 'name' => 'Trainspotting', ), 'traveling' => array ( 'slug' => 'traveling', 'name' => 'Traveling', ), 'triathlon' => array ( 'slug' => 'triathlon', 'name' => 'Triathlon', ), 'ultimate disc' => array ( 'slug' => 'ultimate disc', 'name' => 'Ultimate Disc', ), 'urban exploration' => array ( 'slug' => 'urban exploration', 'name' => 'Urban Exploration', ), 'vacation' => array ( 'slug' => 'vacation', 'name' => 'Vacation', ), 'vehicle restoration' => array ( 'slug' => 'vehicle restoration', 'name' => 'Vehicle Restoration', ), 'video game collecting' => array ( 'slug' => 'video game collecting', 'name' => 'Video Game Collecting', ), 'video gaming' => array ( 'slug' => 'video gaming', 'name' => 'Video Gaming', ), 'videophilia' => array ( 'slug' => 'videophilia', 'name' => 'Videophilia', ), 'vintage cars' => array ( 'slug' => 'vintage cars', 'name' => 'Vintage Cars', ), 'volleyball' => array ( 'slug' => 'volleyball', 'name' => 'Volleyball', ), 'volunteer work / community involvement' => array ( 'slug' => 'volunteer work / community involvement', 'name' => 'Volunteer Work / Community Involvement', ), 'walking' => array ( 'slug' => 'walking', 'name' => 'Walking', ), 'watching movies' => array ( 'slug' => 'watching movies', 'name' => 'Watching Movies', ), 'watching television' => array ( 'slug' => 'watching television', 'name' => 'Watching Television', ), 'water sports' => array ( 'slug' => 'water sports', 'name' => 'Water Sports', ), 'web search' => array ( 'slug' => 'web search', 'name' => 'Web Search', ), 'web surfing' => array ( 'slug' => 'web surfing', 'name' => 'Web Surfing', ), 'whale watching' => array ( 'slug' => 'whale watching', 'name' => 'Whale Watching', ), 'whittling' => array ( 'slug' => 'whittling', 'name' => 'Whittling', ), 'wood carving' => array ( 'slug' => 'wood carving', 'name' => 'Wood Carving', ), 'woodworking' => array ( 'slug' => 'woodworking', 'name' => 'Woodworking', ), 'worldbuilding' => array ( 'slug' => 'worldbuilding', 'name' => 'Worldbuilding', ), 'writing' => array ( 'slug' => 'writing', 'name' => 'Writing', ), 'yoga' => array ( 'slug' => 'yoga', 'name' => 'Yoga', ), 'yo-yoing' => array ( 'slug' => 'yo-yoing', 'name' => 'Yo-Yoing', )),

    'business_services' => [ 'accountants' => array ( 'slug' => 'accountants', 'name' => 'Accountants', ), 'advertising / public relations' => array ( 'slug' => 'advertising / public relations', 'name' => 'Advertising / Public Relations', ), 'aerospace, defense contractors' => array ( 'slug' => 'aerospace, defense contractors', 'name' => 'Aerospace, Defense Contractors', ), 'agribusiness' => array ( 'slug' => 'agribusiness', 'name' => 'Agribusiness', ), 'agricultural services & products' => array ( 'slug' => 'agricultural services & products', 'name' => 'Agricultural Services & Products', ), 'air transport' => array ( 'slug' => 'air transport', 'name' => 'Air Transport', ), 'air transport unions' => array ( 'slug' => 'air transport unions', 'name' => 'Air Transport Unions', ), 'airlines' => array ( 'slug' => 'airlines', 'name' => 'Airlines', ), 'alcoholic beverages' => array ( 'slug' => 'alcoholic beverages', 'name' => 'Alcoholic Beverages', ), 'alternative energy production & services' => array ( 'slug' => 'alternative energy production & services', 'name' => 'Alternative Energy Production & Services', ), 'architectural services' => array ( 'slug' => 'architectural services', 'name' => 'Architectural Services', ), 'attorneys / law firms' => array ( 'slug' => 'attorneys / law firms', 'name' => 'Attorneys / Law Firms', ), 'auto dealers' => array ( 'slug' => 'auto dealers', 'name' => 'Auto Dealers', ), 'auto dealers, japanese' => array ( 'slug' => 'auto dealers, japanese', 'name' => 'Auto Dealers, Japanese', ), 'auto manufacturers' => array ( 'slug' => 'auto manufacturers', 'name' => 'Auto Manufacturers', ), 'automotive' => array ( 'slug' => 'automotive', 'name' => 'Automotive', ), 'banking, mortgage' => array ( 'slug' => 'banking, mortgage', 'name' => 'Banking, Mortgage', ), 'banks, commercial' => array ( 'slug' => 'banks, commercial', 'name' => 'Banks, Commercial', ), 'banks, savings & loans' => array ( 'slug' => 'banks, savings & loans', 'name' => 'Banks, Savings & Loans', ), 'bars & restaurants' => array ( 'slug' => 'bars & restaurants', 'name' => 'Bars & Restaurants', ), 'beer, wine & liquor' => array ( 'slug' => 'beer, wine & liquor', 'name' => 'Beer, Wine & Liquor', ), 'books, magazines & newspapers' => array ( 'slug' => 'books, magazines & newspapers', 'name' => 'Books, Magazines & Newspapers', ), 'broadcasters, radio / tv' => array ( 'slug' => 'broadcasters, radio / tv', 'name' => 'Broadcasters, Radio / TV', ), 'builders / general contractors' => array ( 'slug' => 'builders / general contractors', 'name' => 'Builders / General Contractors', ), 'builders / residential' => array ( 'slug' => 'builders / residential', 'name' => 'Builders / Residential', ), 'building materials & equipment' => array ( 'slug' => 'building materials & equipment', 'name' => 'Building Materials & Equipment', ), 'building trade unions' => array ( 'slug' => 'building trade unions', 'name' => 'Building Trade Unions', ), 'business associations' => array ( 'slug' => 'business associations', 'name' => 'Business Associations', ), 'business services' => array ( 'slug' => 'business services', 'name' => 'Business Services', ), 'cable & satellite tv production & distribution' => array ( 'slug' => 'cable & satellite tv production & distribution', 'name' => 'Cable & Satellite TV Production & Distribution', ), 'candidate committees' => array ( 'slug' => 'candidate committees', 'name' => 'Candidate Committees', ), 'candidate committees, democratic' => array ( 'slug' => 'candidate committees, democratic', 'name' => 'Candidate Committees, Democratic', ), 'candidate committees, republican' => array ( 'slug' => 'candidate committees, republican', 'name' => 'Candidate Committees, Republican', ), 'car dealers' => array ( 'slug' => 'car dealers', 'name' => 'Car Dealers', ), 'car dealers, imports' => array ( 'slug' => 'car dealers, imports', 'name' => 'Car Dealers, Imports', ), 'car manufacturers' => array ( 'slug' => 'car manufacturers', 'name' => 'Car Manufacturers', ), 'casinos / gambling' => array ( 'slug' => 'casinos / gambling', 'name' => 'Casinos / Gambling', ), 'cattle ranchers / livestock' => array ( 'slug' => 'cattle ranchers / livestock', 'name' => 'Cattle Ranchers / Livestock', ), 'chemical & related manufacturing' => array ( 'slug' => 'chemical & related manufacturing', 'name' => 'Chemical & Related Manufacturing', ), 'chiropractors' => array ( 'slug' => 'chiropractors', 'name' => 'Chiropractors', ), 'civil servants / public officials' => array ( 'slug' => 'civil servants / public officials', 'name' => 'Civil Servants / Public Officials', ), 'clergy & religious organizations' => array ( 'slug' => 'clergy & religious organizations', 'name' => 'Clergy & Religious Organizations', ), 'clothing manufacturing' => array ( 'slug' => 'clothing manufacturing', 'name' => 'Clothing Manufacturing', ), 'coal mining' => array ( 'slug' => 'coal mining', 'name' => 'Coal Mining', ), 'colleges, universities & schools' => array ( 'slug' => 'colleges, universities & schools', 'name' => 'Colleges, Universities & Schools', ), 'commercial banks' => array ( 'slug' => 'commercial banks', 'name' => 'Commercial Banks', ), 'commercial tv & radio stations' => array ( 'slug' => 'commercial tv & radio stations', 'name' => 'Commercial TV & Radio Stations', ), 'communications / electronics' => array ( 'slug' => 'communications / electronics', 'name' => 'Communications / Electronics', ), 'computer software' => array ( 'slug' => 'computer software', 'name' => 'Computer Software', ), 'conservative / republican' => array ( 'slug' => 'conservative / republican', 'name' => 'Conservative / Republican', ), 'construction' => array ( 'slug' => 'construction', 'name' => 'Construction', ), 'construction services' => array ( 'slug' => 'construction services', 'name' => 'Construction Services', ), 'construction unions' => array ( 'slug' => 'construction unions', 'name' => 'Construction Unions', ), 'credit unions' => array ( 'slug' => 'credit unions', 'name' => 'Credit Unions', ), 'crop production & basic processing' => array ( 'slug' => 'crop production & basic processing', 'name' => 'Crop Production & Basic Processing', ), 'cruise lines' => array ( 'slug' => 'cruise lines', 'name' => 'Cruise Lines', ), 'cruise ships & lines' => array ( 'slug' => 'cruise ships & lines', 'name' => 'Cruise Ships & Lines', ), 'dairy' => array ( 'slug' => 'dairy', 'name' => 'Dairy', ), 'defense' => array ( 'slug' => 'defense', 'name' => 'Defense', ), 'defense aerospace' => array ( 'slug' => 'defense aerospace', 'name' => 'Defense Aerospace', ), 'defense electronics' => array ( 'slug' => 'defense electronics', 'name' => 'Defense Electronics', ), 'defense / foreign policy advocates' => array ( 'slug' => 'defense / foreign policy advocates', 'name' => 'Defense / Foreign Policy Advocates', ), 'democratic candidate committees' => array ( 'slug' => 'democratic candidate committees', 'name' => 'Democratic Candidate Committees', ), 'democratic leadership pacs' => array ( 'slug' => 'democratic leadership pacs', 'name' => 'Democratic Leadership PACs', ), 'democratic / liberal' => array ( 'slug' => 'democratic / liberal', 'name' => 'Democratic / Liberal', ), 'dentists' => array ( 'slug' => 'dentists', 'name' => 'Dentists', ), 'doctors & other health professionals' => array ( 'slug' => 'doctors & other health professionals', 'name' => 'Doctors & Other Health Professionals', ), 'drug manufacturers' => array ( 'slug' => 'drug manufacturers', 'name' => 'Drug Manufacturers', ), 'education' => array ( 'slug' => 'education', 'name' => 'Education', ), 'electric utilities' => array ( 'slug' => 'electric utilities', 'name' => 'Electric Utilities', ), 'electronics manufacturing & equipment' => array ( 'slug' => 'electronics manufacturing & equipment', 'name' => 'Electronics Manufacturing & Equipment', ), 'electronics, defense contractors' => array ( 'slug' => 'electronics, defense contractors', 'name' => 'Electronics, Defense Contractors', ), 'energy & natural resources' => array ( 'slug' => 'energy & natural resources', 'name' => 'Energy & Natural Resources', ), 'entertainment industry' => array ( 'slug' => 'entertainment industry', 'name' => 'Entertainment Industry', ), 'environment' => array ( 'slug' => 'environment', 'name' => 'Environment', ), 'farm bureaus' => array ( 'slug' => 'farm bureaus', 'name' => 'Farm Bureaus', ), 'farming' => array ( 'slug' => 'farming', 'name' => 'Farming', ), 'finance / credit companies' => array ( 'slug' => 'finance / credit companies', 'name' => 'Finance / Credit Companies', ), 'finance, insurance & real estate' => array ( 'slug' => 'finance, insurance & real estate', 'name' => 'Finance, Insurance & Real Estate', ), 'food & beverage' => array ( 'slug' => 'food & beverage', 'name' => 'Food & Beverage', ), 'food processing & sales' => array ( 'slug' => 'food processing & sales', 'name' => 'Food Processing & Sales', ), 'food products manufacturing' => array ( 'slug' => 'food products manufacturing', 'name' => 'Food Products Manufacturing', ), 'food stores' => array ( 'slug' => 'food stores', 'name' => 'Food Stores', ), 'foreign & defense policy' => array ( 'slug' => 'foreign & defense policy', 'name' => 'Foreign & Defense Policy', ), 'forestry & forest products' => array ( 'slug' => 'forestry & forest products', 'name' => 'Forestry & Forest Products', ), 'for-profit education' => array ( 'slug' => 'for-profit education', 'name' => 'For-Profit Education', ), 'for-profit prisons' => array ( 'slug' => 'for-profit prisons', 'name' => 'For-Profit Prisons', ), 'foundations, philanthropists & non-profits' => array ( 'slug' => 'foundations, philanthropists & non-profits', 'name' => 'Foundations, Philanthropists & Non-Profits', ), 'funeral services' => array ( 'slug' => 'funeral services', 'name' => 'Funeral Services', ), 'gambling & casinos' => array ( 'slug' => 'gambling & casinos', 'name' => 'Gambling & Casinos', ), 'gambling, indian casinos' => array ( 'slug' => 'gambling, indian casinos', 'name' => 'Gambling, Indian Casinos', ), 'garbage collection / waste management' => array ( 'slug' => 'garbage collection / waste management', 'name' => 'Garbage Collection / Waste Management', ), 'gas & oil' => array ( 'slug' => 'gas & oil', 'name' => 'Gas & Oil', ), 'gay & lesbian rights & issues' => array ( 'slug' => 'gay & lesbian rights & issues', 'name' => 'Gay & Lesbian Rights & Issues', ), 'general contractors' => array ( 'slug' => 'general contractors', 'name' => 'General Contractors', ), 'government employee unions' => array ( 'slug' => 'government employee unions', 'name' => 'Government Employee Unions', ), 'government employees' => array ( 'slug' => 'government employees', 'name' => 'Government Employees', ), 'gun control' => array ( 'slug' => 'gun control', 'name' => 'Gun Control', ), 'gun rights' => array ( 'slug' => 'gun rights', 'name' => 'Gun Rights', ), 'health' => array ( 'slug' => 'health', 'name' => 'Health', ), 'health professionals' => array ( 'slug' => 'health professionals', 'name' => 'Health Professionals', ), 'health services / hmos' => array ( 'slug' => 'health services / hmos', 'name' => 'Health Services / HMOs', ), 'hedge funds' => array ( 'slug' => 'hedge funds', 'name' => 'Hedge Funds', ), 'hmos & health care services' => array ( 'slug' => 'hmos & health care services', 'name' => 'HMOs & Health Care Services', ), 'home builders' => array ( 'slug' => 'home builders', 'name' => 'Home Builders', ), 'hospitals & nursing homes' => array ( 'slug' => 'hospitals & nursing homes', 'name' => 'Hospitals & Nursing Homes', ), 'hotels, motels & tourism' => array ( 'slug' => 'hotels, motels & tourism', 'name' => 'Hotels, Motels & Tourism', ), 'human rights' => array ( 'slug' => 'human rights', 'name' => 'Human Rights', ), 'ideological / single-issue' => array ( 'slug' => 'ideological / single-issue', 'name' => 'Ideological / Single-Issue', ), 'indian gaming' => array ( 'slug' => 'indian gaming', 'name' => 'Indian Gaming', ), 'industrial unions' => array ( 'slug' => 'industrial unions', 'name' => 'Industrial Unions', ), 'insurance' => array ( 'slug' => 'insurance', 'name' => 'Insurance', ), 'internet' => array ( 'slug' => 'internet', 'name' => 'Internet', ), 'israel policy' => array ( 'slug' => 'israel policy', 'name' => 'Israel Policy', ), 'labor' => array ( 'slug' => 'labor', 'name' => 'Labor', ), 'lawyers & lobbyists' => array ( 'slug' => 'lawyers & lobbyists', 'name' => 'Lawyers & Lobbyists', ), 'lawyers / law firms' => array ( 'slug' => 'lawyers / law firms', 'name' => 'Lawyers / Law Firms', ), 'leadership pacs' => array ( 'slug' => 'leadership pacs', 'name' => 'Leadership PACs', ), 'liberal / democratic' => array ( 'slug' => 'liberal / democratic', 'name' => 'Liberal / Democratic', ), 'liquor, wine & beer' => array ( 'slug' => 'liquor, wine & beer', 'name' => 'Liquor, Wine & Beer', ), 'livestock' => array ( 'slug' => 'livestock', 'name' => 'Livestock', ), 'lobbyists' => array ( 'slug' => 'lobbyists', 'name' => 'Lobbyists', ), 'lodging / tourism' => array ( 'slug' => 'lodging / tourism', 'name' => 'Lodging / Tourism', ), 'logging, timber & paper mills' => array ( 'slug' => 'logging, timber & paper mills', 'name' => 'Logging, Timber & Paper Mills', ), 'manufacturing, misc' => array ( 'slug' => 'manufacturing, misc', 'name' => 'Manufacturing, Misc', ), 'marine transport' => array ( 'slug' => 'marine transport', 'name' => 'Marine Transport', ), 'meat processing & products' => array ( 'slug' => 'meat processing & products', 'name' => 'Meat Processing & products', ), 'medical supplies' => array ( 'slug' => 'medical supplies', 'name' => 'Medical Supplies', ), 'mining' => array ( 'slug' => 'mining', 'name' => 'Mining', ), 'misc business' => array ( 'slug' => 'misc business', 'name' => 'Misc Business', ), 'misc finance' => array ( 'slug' => 'misc finance', 'name' => 'Misc Finance', ), 'misc manufacturing & distributing' => array ( 'slug' => 'misc manufacturing & distributing', 'name' => 'Misc Manufacturing & Distributing', ), 'misc unions' => array ( 'slug' => 'misc unions', 'name' => 'Misc Unions', ), 'miscellaneous defense' => array ( 'slug' => 'miscellaneous defense', 'name' => 'Miscellaneous Defense', ), 'miscellaneous services' => array ( 'slug' => 'miscellaneous services', 'name' => 'Miscellaneous Services', ), 'mortgage bankers & brokers' => array ( 'slug' => 'mortgage bankers & brokers', 'name' => 'Mortgage Bankers & Brokers', ), 'motion picture production & distribution' => array ( 'slug' => 'motion picture production & distribution', 'name' => 'Motion Picture Production & Distribution', ), 'music production' => array ( 'slug' => 'music production', 'name' => 'Music Production', ), 'natural gas pipelines' => array ( 'slug' => 'natural gas pipelines', 'name' => 'Natural Gas Pipelines', ), 'newspaper, magazine & book publishing' => array ( 'slug' => 'newspaper, magazine & book publishing', 'name' => 'Newspaper, Magazine & Book Publishing', ), 'non-profits, foundations & philanthropists' => array ( 'slug' => 'non-profits, foundations & philanthropists', 'name' => 'Non-profits, Foundations & Philanthropists', ), 'nurses' => array ( 'slug' => 'nurses', 'name' => 'Nurses', ), 'nursing homes / hospitals' => array ( 'slug' => 'nursing homes / hospitals', 'name' => 'Nursing Homes / Hospitals', ), 'nutritional & dietary supplements' => array ( 'slug' => 'nutritional & dietary supplements', 'name' => 'Nutritional & Dietary Supplements', ), 'oil & gas' => array ( 'slug' => 'oil & gas', 'name' => 'Oil & Gas', ), 'other' => array ( 'slug' => 'other', 'name' => 'Other', ), 'payday lenders' => array ( 'slug' => 'payday lenders', 'name' => 'Payday Lenders', ), 'pharmaceutical manufacturing' => array ( 'slug' => 'pharmaceutical manufacturing', 'name' => 'Pharmaceutical Manufacturing', ), 'pharmaceuticals / health products' => array ( 'slug' => 'pharmaceuticals / health products', 'name' => 'Pharmaceuticals / Health Products', ), 'phone companies' => array ( 'slug' => 'phone companies', 'name' => 'Phone Companies', ), 'physicians & other health professionals' => array ( 'slug' => 'physicians & other health professionals', 'name' => 'Physicians & Other Health Professionals', ), 'postal unions' => array ( 'slug' => 'postal unions', 'name' => 'Postal Unions', ), 'poultry & eggs' => array ( 'slug' => 'poultry & eggs', 'name' => 'Poultry & Eggs', ), 'power utilities' => array ( 'slug' => 'power utilities', 'name' => 'Power Utilities', ), 'printing & publishing' => array ( 'slug' => 'printing & publishing', 'name' => 'Printing & Publishing', ), 'private equity & investment firms' => array ( 'slug' => 'private equity & investment firms', 'name' => 'Private Equity & Investment Firms', ), 'professional sports, sports arenas & related equipment & services' => array ( 'slug' => 'professional sports, sports arenas & related equipment & services', 'name' => 'Professional Sports, Sports Arenas & Related Equipment & Services', ), 'progressive / democratic' => array ( 'slug' => 'progressive / democratic', 'name' => 'Progressive / Democratic', ), 'pro-israel' => array ( 'slug' => 'pro-israel', 'name' => 'Pro-Israel', ), 'public employees' => array ( 'slug' => 'public employees', 'name' => 'Public Employees', ), 'public sector unions' => array ( 'slug' => 'public sector unions', 'name' => 'Public Sector Unions', ), 'publishing & printing' => array ( 'slug' => 'publishing & printing', 'name' => 'Publishing & Printing', ), 'radio / tv stations' => array ( 'slug' => 'radio / tv stations', 'name' => 'Radio / TV Stations', ), 'railroads' => array ( 'slug' => 'railroads', 'name' => 'Railroads', ), 'real estate' => array ( 'slug' => 'real estate', 'name' => 'Real Estate', ), 'record companies / singers' => array ( 'slug' => 'record companies / singers', 'name' => 'Record Companies / Singers', ), 'recorded music & music production' => array ( 'slug' => 'recorded music & music production', 'name' => 'Recorded Music & Music Production', ), 'recreation / live entertainment' => array ( 'slug' => 'recreation / live entertainment', 'name' => 'Recreation / Live Entertainment', ), 'religious organizations / clergy' => array ( 'slug' => 'religious organizations / clergy', 'name' => 'Religious Organizations / Clergy', ), 'republican candidate committees' => array ( 'slug' => 'republican candidate committees', 'name' => 'Republican Candidate Committees', ), 'republican leadership pacs' => array ( 'slug' => 'republican leadership pacs', 'name' => 'Republican Leadership PACs', ), 'republican / conservative' => array ( 'slug' => 'republican / conservative', 'name' => 'Republican / Conservative', ), 'residential construction' => array ( 'slug' => 'residential construction', 'name' => 'Residential Construction', ), 'restaurants & drinking establishments' => array ( 'slug' => 'restaurants & drinking establishments', 'name' => 'Restaurants & Drinking Establishments', ), 'retail sales' => array ( 'slug' => 'retail sales', 'name' => 'Retail Sales', ), 'retired' => array ( 'slug' => 'retired', 'name' => 'Retired', ), 'savings & loans' => array ( 'slug' => 'savings & loans', 'name' => 'Savings & Loans', ), 'schools / education' => array ( 'slug' => 'schools / education', 'name' => 'Schools / Education', ), 'sea transport' => array ( 'slug' => 'sea transport', 'name' => 'Sea Transport', ), 'securities & investment' => array ( 'slug' => 'securities & investment', 'name' => 'Securities & Investment', ), 'special trade contractors' => array ( 'slug' => 'special trade contractors', 'name' => 'Special Trade Contractors', ), 'sports, professional' => array ( 'slug' => 'sports, professional', 'name' => 'Sports, Professional', ), 'steel production' => array ( 'slug' => 'steel production', 'name' => 'Steel Production', ), 'stock brokers / investment industry' => array ( 'slug' => 'stock brokers / investment industry', 'name' => 'Stock Brokers / Investment Industry', ), 'student loan companies' => array ( 'slug' => 'student loan companies', 'name' => 'Student Loan Companies', ), 'sugar cane & sugar beets' => array ( 'slug' => 'sugar cane & sugar beets', 'name' => 'Sugar Cane & Sugar Beets', ), 'teachers unions' => array ( 'slug' => 'teachers unions', 'name' => 'Teachers Unions', ), 'teachers / education' => array ( 'slug' => 'teachers / education', 'name' => 'Teachers / Education', ), 'telecom services & equipment' => array ( 'slug' => 'telecom services & equipment', 'name' => 'Telecom Services & Equipment', ), 'telephone utilities' => array ( 'slug' => 'telephone utilities', 'name' => 'Telephone Utilities', ), 'textiles' => array ( 'slug' => 'textiles', 'name' => 'Textiles', ), 'timber, logging & paper mills' => array ( 'slug' => 'timber, logging & paper mills', 'name' => 'Timber, Logging & Paper Mills', ), 'tobacco' => array ( 'slug' => 'tobacco', 'name' => 'Tobacco', ), 'transportation' => array ( 'slug' => 'transportation', 'name' => 'Transportation', ), 'transportation unions' => array ( 'slug' => 'transportation unions', 'name' => 'Transportation Unions', ), 'trash collection / waste management' => array ( 'slug' => 'trash collection / waste management', 'name' => 'Trash Collection / Waste Management', ), 'trucking' => array ( 'slug' => 'trucking', 'name' => 'Trucking', ), 'tv / movies / music' => array ( 'slug' => 'tv / movies / music', 'name' => 'TV / Movies / Music', ), 'tv production' => array ( 'slug' => 'tv production', 'name' => 'TV Production', ), 'unions' => array ( 'slug' => 'unions', 'name' => 'Unions', ), 'unions, airline' => array ( 'slug' => 'unions, airline', 'name' => 'Unions, Airline', ), 'unions, building trades' => array ( 'slug' => 'unions, building trades', 'name' => 'Unions, Building Trades', ), 'unions, industrial' => array ( 'slug' => 'unions, industrial', 'name' => 'Unions, Industrial', ), 'unions, misc' => array ( 'slug' => 'unions, misc', 'name' => 'Unions, Misc', ), 'unions, public sector' => array ( 'slug' => 'unions, public sector', 'name' => 'Unions, Public Sector', ), 'unions, teacher' => array ( 'slug' => 'unions, teacher', 'name' => 'Unions, Teacher', ), 'unions, transportation' => array ( 'slug' => 'unions, transportation', 'name' => 'Unions, Transportation', ), 'universities, colleges & schools' => array ( 'slug' => 'universities, colleges & schools', 'name' => 'Universities, Colleges & Schools', ), 'vegetables & fruits' => array ( 'slug' => 'vegetables & fruits', 'name' => 'Vegetables & Fruits', ), 'venture capital' => array ( 'slug' => 'venture capital', 'name' => 'Venture Capital', ), 'waste management' => array ( 'slug' => 'waste management', 'name' => 'Waste Management', ), 'wine, beer & liquor' => array ( 'slug' => 'wine, beer & liquor', 'name' => 'Wine, Beer & Liquor', ), 'women\'s issues' => array ( 'slug' => 'women\'s issues', 'name' => 'Women\'s Issues', )],

    'industries' => [
        "accounting" => ["slug" => "accounting", "name" => "Accounting"], "airlines/aviation" => ["slug" => "airlines/aviation", "name" => "Airlines/Aviation"], "alternative dispute resolution" => ["slug" => "alternative dispute resolution", "name" => "Alternative Dispute Resolution"], "alternative medicine" => ["slug" => "alternative medicine", "name" => "Alternative Medicine"], "animation" => ["slug" => "animation", "name" => "Animation"], "apparel and fashion" => ["slug" => "apparel and fashion", "name" => "Apparel and Fashion"], "architecture and planning" => ["slug" => "architecture and planning", "name" => "Architecture and Planning"], "arts and crafts" => ["slug" => "arts and crafts", "name" => "Arts and Crafts"], "automotive" => ["slug" => "automotive", "name" => "Automotive"], "aviation and aerospace" => ["slug" => "aviation and aerospace", "name" => "Aviation and Aerospace"], "banking" => ["slug" => "banking", "name" => "Banking"], "biotechnology" => ["slug" => "biotechnology", "name" => "Biotechnology"], "broadcast media" => ["slug" => "broadcast media", "name" => "Broadcast Media"], "building materials" => ["slug" => "building materials", "name" => "Building Materials"], "business supplies and equipment" => ["slug" => "business supplies and equipment", "name" => "Business Supplies and Equipment"], "capital markets" => ["slug" => "capital markets", "name" => "Capital Markets"], "chemicals" => ["slug" => "chemicals", "name" => "Chemicals"], "civic and social organization" => ["slug" => "civic and social organization", "name" => "Civic and Social Organization"], "civil engineering" => ["slug" => "civil engineering", "name" => "Civil Engineering"], "commercial real estate" => ["slug" => "commercial real estate", "name" => "Commercial Real Estate"], "computer and network security" => ["slug" => "computer and network security", "name" => "Computer and Network Security"], "computer games" => ["slug" => "computer games", "name" => "Computer Games"], "computer hardware" => ["slug" => "computer hardware", "name" => "Computer Hardware"], "computer networking" => ["slug" => "computer networking", "name" => "Computer Networking"], "computer software" => ["slug" => "computer software", "name" => "Computer Software"], "construction" => ["slug" => "construction", "name" => "Construction"], "consumer electronics" => ["slug" => "consumer electronics", "name" => "Consumer Electronics"], "consumer goods" => ["slug" => "consumer goods", "name" => "Consumer Goods"], "consumer services" => ["slug" => "consumer services", "name" => "Consumer Services"], "cosmetics" => ["slug" => "cosmetics", "name" => "Cosmetics"], "dairy" => ["slug" => "dairy", "name" => "Dairy"], "defense and space" => ["slug" => "defense and space", "name" => "Defense and Space"], "design" => ["slug" => "design", "name" => "Design"], "education management" => ["slug" => "education management", "name" => "Education Management"], "e-learning" => ["slug" => "e-learning", "name" => "E-Learning"], "electrical/electronic manufacturing" => ["slug" => "electrical/electronic manufacturing", "name" => "Electrical/Electronic Manufacturing"], "entertainment" => ["slug" => "entertainment", "name" => "Entertainment"], "environmental services" => ["slug" => "environmental services", "name" => "Environmental Services"], "events services" => ["slug" => "events services", "name" => "Events Services"], "executive office" => ["slug" => "executive office", "name" => "Executive Office"], "facilities services" => ["slug" => "facilities services", "name" => "Facilities Services"], "farming" => ["slug" => "farming", "name" => "Farming"], "financial services" => ["slug" => "financial services", "name" => "Financial Services"], "fine art" => ["slug" => "fine art", "name" => "Fine Art"], "fishery" => ["slug" => "fishery", "name" => "Fishery"], "food and beverages" => ["slug" => "food and beverages", "name" => "Food and Beverages"], "food production" => ["slug" => "food production", "name" => "Food Production"], "fund-raising" => ["slug" => "fund-raising", "name" => "Fund-Raising"], "furniture" => ["slug" => "furniture", "name" => "Furniture"], "gambling and casinos" => ["slug" => "gambling and casinos", "name" => "Gambling and Casinos"], "glass, ceramics and concrete" => ["slug" => "glass, ceramics and concrete", "name" => "Glass, Ceramics and Concrete"], "government administration" => ["slug" => "government administration", "name" => "Government Administration"], "government relations" => ["slug" => "government relations", "name" => "Government Relations"], "graphic design" => ["slug" => "graphic design", "name" => "Graphic Design"], "health, wellness and fitness" => ["slug" => "health, wellness and fitness", "name" => "Health, Wellness and Fitness"], "higher education" => ["slug" => "higher education", "name" => "Higher Education"], "hospital and health care" => ["slug" => "hospital and health care", "name" => "Hospital and Health Care"], "hospitality" => ["slug" => "hospitality", "name" => "Hospitality"], "human resources" => ["slug" => "human resources", "name" => "Human Resources"], "import and export" => ["slug" => "import and export", "name" => "Import and Export"], "individual and family services" => ["slug" => "individual and family services", "name" => "Individual and Family Services"], "industrial automation" => ["slug" => "industrial automation", "name" => "Industrial Automation"], "information services" => ["slug" => "information services", "name" => "Information Services"], "information technology and services" => ["slug" => "information technology and services", "name" => "Information Technology and Services"], "insurance" => ["slug" => "insurance", "name" => "Insurance"], "international affairs" => ["slug" => "international affairs", "name" => "International Affairs"], "international trade and development" => ["slug" => "international trade and development", "name" => "International Trade and Development"], "internet" => ["slug" => "internet", "name" => "Internet"], "investment banking" => ["slug" => "investment banking", "name" => "Investment Banking"], "investment management" => ["slug" => "investment management", "name" => "Investment Management"], "judiciary" => ["slug" => "judiciary", "name" => "Judiciary"], "law enforcement" => ["slug" => "law enforcement", "name" => "Law Enforcement"], "law practice" => ["slug" => "law practice", "name" => "Law Practice"], "legal services" => ["slug" => "legal services", "name" => "Legal Services"], "legislative office" => ["slug" => "legislative office", "name" => "Legislative Office"], "leisure, travel and tourism" => ["slug" => "leisure, travel and tourism", "name" => "Leisure, Travel and Tourism"], "libraries" => ["slug" => "libraries", "name" => "Libraries"], "logistics and supply chain" => ["slug" => "logistics and supply chain", "name" => "Logistics and Supply Chain"], "luxury goods and jewelry" => ["slug" => "luxury goods and jewelry", "name" => "Luxury Goods and Jewelry"], "machinery" => ["slug" => "machinery", "name" => "Machinery"], "management consulting" => ["slug" => "management consulting", "name" => "Management Consulting"], "maritime" => ["slug" => "maritime", "name" => "Maritime"], "marketing and advertising" => ["slug" => "marketing and advertising", "name" => "Marketing and Advertising"], "market research" => ["slug" => "market research", "name" => "Market Research"], "mechanical or industrial engineering" => ["slug" => "mechanical or industrial engineering", "name" => "Mechanical or Industrial Engineering"], "media production" => ["slug" => "media production", "name" => "Media Production"], "medical devices" => ["slug" => "medical devices", "name" => "Medical Devices"], "medical practice" => ["slug" => "medical practice", "name" => "Medical Practice"], "mental health care" => ["slug" => "mental health care", "name" => "Mental Health Care"], "military" => ["slug" => "military", "name" => "Military"], "mining and metals" => ["slug" => "mining and metals", "name" => "Mining and Metals"], "motion pictures and film" => ["slug" => "motion pictures and film", "name" => "Motion Pictures and Film"], "museums and institutions" => ["slug" => "museums and institutions", "name" => "Museums and Institutions"], "music" => ["slug" => "music", "name" => "Music"], "nanotechnology" => ["slug" => "nanotechnology", "name" => "Nanotechnology"], "newspapers" => ["slug" => "newspapers", "name" => "Newspapers"], "nonprofit organization management" => ["slug" => "nonprofit organization management", "name" => "Nonprofit Organization Management"], "oil and energy" => ["slug" => "oil and energy", "name" => "Oil and Energy"], "online media" => ["slug" => "online media", "name" => "Online Media"], "outsourcing/offshoring" => ["slug" => "outsourcing/offshoring", "name" => "Outsourcing/Offshoring"], "package/freight delivery" => ["slug" => "package/freight delivery", "name" => "Package/Freight Delivery"], "packaging and containers" => ["slug" => "packaging and containers", "name" => "Packaging and Containers"], "paper and forest products" => ["slug" => "paper and forest products", "name" => "Paper and Forest Products"], "performing arts" => ["slug" => "performing arts", "name" => "Performing Arts"], "pharmaceuticals" => ["slug" => "pharmaceuticals", "name" => "Pharmaceuticals"], "philanthropy" => ["slug" => "philanthropy", "name" => "Philanthropy"], "photography" => ["slug" => "photography", "name" => "Photography"], "plastics" => ["slug" => "plastics", "name" => "Plastics"], "political organization" => ["slug" => "political organization", "name" => "Political Organization"], "primary/secondary education" => ["slug" => "primary/secondary education", "name" => "Primary/Secondary Education"], "printing" => ["slug" => "printing", "name" => "Printing"], "professional training and coaching" => ["slug" => "professional training and coaching", "name" => "Professional Training and Coaching"], "program development" => ["slug" => "program development", "name" => "Program Development"], "public policy" => ["slug" => "public policy", "name" => "Public Policy"], "public relations and communications" => ["slug" => "public relations and communications", "name" => "Public Relations and Communications"], "public safety" => ["slug" => "public safety", "name" => "Public Safety"], "publishing" => ["slug" => "publishing", "name" => "Publishing"], "railroad manufacture" => ["slug" => "railroad manufacture", "name" => "Railroad Manufacture"], "ranching" => ["slug" => "ranching", "name" => "Ranching"], "real estate" => ["slug" => "real estate", "name" => "Real Estate"], "recreational facilities and services" => ["slug" => "recreational facilities and services", "name" => "Recreational Facilities and Services"], "religious institutions" => ["slug" => "religious institutions", "name" => "Religious Institutions"], "renewables and environment" => ["slug" => "renewables and environment", "name" => "Renewables and Environment"], "research" => ["slug" => "research", "name" => "Research"], "restaurants" => ["slug" => "restaurants", "name" => "Restaurants"], "retail" => ["slug" => "retail", "name" => "Retail"], "security and investigations" => ["slug" => "security and investigations", "name" => "Security and Investigations"], "semiconductors" => ["slug" => "semiconductors", "name" => "Semiconductors"], "shipbuilding" => ["slug" => "shipbuilding", "name" => "Shipbuilding"], "sporting goods" => ["slug" => "sporting goods", "name" => "Sporting Goods"], "sports" => ["slug" => "sports", "name" => "Sports"], "staffing and recruiting" => ["slug" => "staffing and recruiting", "name" => "Staffing and Recruiting"], "supermarkets" => ["slug" => "supermarkets", "name" => "Supermarkets"], "telecommunications" => ["slug" => "telecommunications", "name" => "Telecommunications"], "textiles" => ["slug" => "textiles", "name" => "Textiles"], "think tanks" => ["slug" => "think tanks", "name" => "Think Tanks"], "tobacco" => ["slug" => "tobacco", "name" => "Tobacco"], "translation and localization" => ["slug" => "translation and localization", "name" => "Translation and Localization"], "transportation/trucking/railroad" => ["slug" => "transportation/trucking/railroad", "name" => "Transportation/Trucking/Railroad"], "utilities" => ["slug" => "utilities", "name" => "Utilities"], "venture capital and private equity" => ["slug" => "venture capital and private equity", "name" => "Venture Capital and Private Equity"], "veterinary" => ["slug" => "veterinary", "name" => "Veterinary"], "warehousing" => ["slug" => "warehousing", "name" => "Warehousing"], "wholesale" => ["slug" => "wholesale", "name" => "Wholesale"], "wine and spirits" => ["slug" => "wine and spirits", "name" => "Wine and Spirits"], "wireless" => ["slug" => "wireless", "name" => "Wireless"], "writing and editing" => ["slug" => "writing and editing", "name" => "Writing and Editing"]
    ],

    'package' => [
        'prime-member' => ['slug' => 'prime-member', 'name' => 'PRIME MEMBER', 'facility_category' => null ],
        'hot-desk' => ['slug' => 'hot-desk', 'name' => 'HOT DESK', 'facility_category' => 0],
        'fixed-desk' => ['slug' => 'fixed-desk', 'name' => 'FIXED DESK', 'facility_category' => 1],
        'private-office' => ['slug' => 'private-office', 'name' => 'PRIVATE OFFICE', 'facility_category' => 2]
    ],

    'packages' => [
        '0' => [
            'slug' => '0',
            'name' => 'Prime Member',
	        'facility_category' => null,
            'pricing_rule' => [2],
            'pricing_rule_for_deposit' => [],
            'complimentary' => [
                2 => [3, 4]
            ]
        ],
        '1' => [
            'slug' => '1',
            'name' => 'Hot Desk',
	        'facility_category' => 0,
            'pricing_rule' => [2],
            'pricing_rule_for_deposit' => [],
             'complimentary' => []
        ],
        '2' => [
            'slug' => '2',
            'name' => 'Fixed Desk',
	        'facility_category' => 1,
            'pricing_rule' => [2],
            'pricing_rule_for_deposit' => [],
            'complimentary' => []
        ],
        '3' => [
            'slug' => '3',
            'name' => 'Private Office',
	        'facility_category' => 2,
            'pricing_rule' => [2],
            'pricing_rule_for_deposit' => [],
            'complimentary' => []
        ]
    ],

    'tax' => [
        'my' => ['slug' => 'gst', 'name' => 'GST', 'value' => 7]
    ],

    'facility_category' => [
        '0' => [
                    'slug' => '0',
                    'name' => 'Hot Desk',
                    'view' => array('package' => 'hot_desk'),
                    'has_seat_feature' => '1',
                    'pricing_rule' => [0, 1, 2],
                    'pricing_rule_for_member_special_price' => [0, 1],
                    'pricing_rule_for_deposit' => [2],
                    'complimentary' => [
                        2  => [3, 4]
                    ],
                    'link_to_member_portal' => [
                        'flag' => true,
                        'flow' => 0
                    ]
                ],
        '1' => [
                'slug' => '1',
                'name' => 'Fixed Desk',
                'view' => array('package' => 'fixed_desk'),
                'has_seat_feature' => '1',
                'pricing_rule' => [0, 1, 2],
                'pricing_rule_for_member_special_price' => [0, 1],
                'pricing_rule_for_deposit' => [2],
                'complimentary' => [
                    2 => [3, 4]
                ],
                'link_to_member_portal' => [
                    'flag' => true,
                    'flow' => 0
                ]
            ],
        '2' => [
                'slug' => '2',
                'name' => 'Private Office',
                'view' => array('package' => 'private_office'),
                'has_seat_feature' => '1',
                'pricing_rule' => [0, 1, 2],
                'pricing_rule_for_member_special_price' => [0, 1],
                'pricing_rule_for_deposit' => [2],
                'complimentary' => [
                    2 => [3, 4]
                ],
                'link_to_member_portal' => [
                    'flag' => true,
                    'flow' => 0
                ]
            ],
        '3' => [
                'slug' => '3',
                'name' => 'Meeting Room',
                'view' => array('package' => ''),
                'has_seat_feature' => '1',
                'pricing_rule' => [0,1],
                'pricing_rule_for_member_special_price' => [0, 1],
                'pricing_rule_for_deposit' => [],
                'complimentary' => [],
                'link_to_member_portal' => [
                    'flag' => true,
                    'flow' => 1
                ]
            ],
        '4' => [
                'slug' => '4',
                'name' => 'Printer',
                'view' => array('package' => ''),
                'has_seat_feature' => '0',
                'pricing_rule' => [3, 4],
                'pricing_rule_for_member_special_price' => [],
                'pricing_rule_for_deposit' => [],
                'complimentary' => [],
                'link_to_member_portal' => [
                    'flag' => false,
                    'flow' => 0
                ]
            ]
    ],

    'pricing_rule' => [
        '0' => ['slug' => '0', 'name' => 'Per Hour'],
        '1' => ['slug' => '1', 'name' => 'Per Day'],
        '2' => ['slug' => '2', 'name' => 'Per Month'],
        '3' => ['slug' => '3', 'name' => 'Per Page (Black)'],
        '4' => ['slug' => '4', 'name' => 'Per Page (Color)']
    ],

    'wallet_transaction_type' => [
        '0' => ['slug' => 0, 'name' => 'Top-Up'],
        '1' => ['slug' => 1, 'name' => 'Facility Booking'],
        '2' => ['slug' => 2, 'name' => 'Cancel Facility Booking'],
        '3' => ['slug' => 3, 'name' => 'Withdrawal'],
        '4' => ['slug' => 4, 'name' => 'Penalty Charge for Facility Booking'],
    ],

    'subscription_status' => [
        '0' => ['slug' => 0, 'name' => 'Confirmed'],
        '1' => ['slug' => 1, 'name' => 'Checked In'],
        '2' => ['slug' => 2, 'name' => 'Checked Out'],
        '3' => ['slug' => 3, 'name' => 'Void']
    ],

    'subscription_invoice_transaction_status' => [
        '0' => ['slug' => 0, 'name' => 'Package Charge'],
        '1' => ['slug' => 1, 'name' => 'Package Discount'],
        '2' => ['slug' => 2, 'name' => 'Package Tax Charge'],
        '3' => ['slug' => 3, 'name' => 'Deposit Charge'],
        '4' => ['slug' => 4, 'name' => 'Package Paid'],
        '5' => ['slug' => 5, 'name' => 'Deposit Paid'],
        '6' => ['slug' => 6, 'name' => 'Deposit Refund'],
    ],

    'reservation_status' => [
        '0' => ['slug' => 0, 'name' => 'Confirmed'],
        '1' => ['slug' => 1, 'name' => 'Cancel']
    ],


    'reservation_room_cancellation_policy' => [
        array('rule' => array(240, null), 'charge' => 0, 'message' => 'No charge for 4 hours onwards before meeting room booking time.'),
        array('rule' => array(180, 240), 'charge' => 50, 'message' => '50% charges for 3 - 4 hours before meeting room booking time.'),
        array('rule' => array(120, 180), 'charge' => 75, 'message' => '75% charges for 2 - 3 hours before meeting room booking time.'),
        array('rule' => array(0, 120), 'charge' => 100, 'message' => '100% charges for 0 - 2 hours before meeting room booking time.')
    ],

    'booking_status' => [
        '0' => ['slug' => 0, 'name' => 'Reserved'],
    ],

    'invoice_status' => [
        '0' => ['slug' => 0, 'name' => 'Unpaid'],
        '1' => ['slug' => 1, 'name' => 'Partially Paid'],
        '2' => ['slug' => 2, 'name' => 'Paid'],
        '3' => ['slug' => 3, 'name' => 'Overpaid'],
        '4' => ['slug' => 4, 'name' => 'Refund'],
        '5' => ['slug' => 5, 'name' => 'Void']
    ],

    'payment_method' => [
        '3' => ['slug' => 3, 'name' => 'Bank Transfer'],
        '0' => ['slug' => 0, 'name' => 'Cash'],
        '1' => ['slug' => 1, 'name' => 'Check'],
        '2' => ['slug' => 2, 'name' => 'Credit Card'],

    ],

    'payment_mode' => [
        '0' => ['slug' => 0, 'name' => 'Credit'],
        '1' => ['slug' => 1, 'name' => 'Debit'],
    ],

    'payment_status' => [
        '0' => ['slug' => 0, 'name' => 'Failed'],
        '1' => ['slug' => 1, 'name' => 'Succeed'],
        '2' => ['slug' => 2, 'name' => 'Void']
    ],

    'transaction_type' => [
        '0' => ['slug' => 0, 'name' => 'Subscription'],
        '1' => ['slug' => 1, 'name' => 'Wallet']
    ],

    'time' => [
        '12:00am' => ['slug' => '12:00am', 'name' => '12:00am'],
        '12:30am' => ['slug' => '12:30am', 'name' => '12:30am'],
        '1:00am' => ['slug' => '1:00am', 'name' => '1:00am'],
        '1:30am' => ['slug' => '1:30am', 'name' => '1:30am'],
        '2:00am' => ['slug' => '2:00am', 'name' => '2:00am'],
        '2:30am' => ['slug' => '2:30am', 'name' => '2:30am'],
        '3:00am' => ['slug' => '3:00am', 'name' => '3:00am'],
        '3:30am' => ['slug' => '3:30am', 'name' => '3:30am'],
        '4:00am' => ['slug' => '4:00am', 'name' => '4:00am'],
        '4:30am' => ['slug' => '4:30am', 'name' => '4:30am'],
        '5:00am' => ['slug' => '5:00am', 'name' => '5:00am'],
        '5:30am' => ['slug' => '5:30am', 'name' => '5:30am'],
        '6:00am' => ['slug' => '6:00am', 'name' => '6:00am'],
        '6:30am' => ['slug' => '6:30am', 'name' => '6:30am'],
        '7:00am' => ['slug' => '7:00am', 'name' => '7:00am'],
        '7:30am' => ['slug' => '7:30am', 'name' => '7:30am'],
        '8:00am' => ['slug' => '8:00am', 'name' => '8:00am'],
        '8:30am' => ['slug' => '8:30am', 'name' => '8:30am'],
        '9:00am' => ['slug' => '9:00am', 'name' => '9:00am'],
        '9:30am' => ['slug' => '9:30am', 'name' => '9:30am'],
        '10:00am' => ['slug' => '10:00am', 'name' => '10:00am'],
        '10:30am' => ['slug' => '10:30am', 'name' => '10:30am'],
        '11:00am' => ['slug' => '11:00am', 'name' => '11:00am'],
        '11:30am' => ['slug' => '11:30am', 'name' => '11:30am'],
        '12:00pm' => ['slug' => '12:00pm', 'name' => '12:00pm'],
        '12:30pm' => ['slug' => '12:30pm', 'name' => '12:30pm'],
        '1:00pm' => ['slug' => '1:00pm', 'name' => '1:00pm'],
        '1:30pm' => ['slug' => '1:30pm', 'name' => '1:30pm'],
        '2:00pm' => ['slug' => '2:00pm', 'name' => '2:00pm'],
        '2:30pm' => ['slug' => '2:30pm', 'name' => '2:30pm'],
        '3:00pm' => ['slug' => '3:00pm', 'name' => '3:00pm'],
        '3:30pm' => ['slug' => '3:30pm', 'name' => '3:30pm'],
        '4:00pm' => ['slug' => '4:00pm', 'name' => '4:00pm'],
        '4:30pm' => ['slug' => '4:30pm', 'name' => '4:30pm'],
        '5:00pm' => ['slug' => '5:00pm', 'name' => '5:00pm'],
        '5:30pm' => ['slug' => '5:30pm', 'name' => '5:30pm'],
        '6:00pm' => ['slug' => '6:00pm', 'name' => '6:00pm'],
        '6:30pm' => ['slug' => '6:30pm', 'name' => '6:30pm'],
        '7:00pm' => ['slug' => '7:00pm', 'name' => '7:00pm'],
        '7:30pm' => ['slug' => '7:30pm', 'name' => '7:30pm'],
        '8:00pm' => ['slug' => '8:00pm', 'name' => '8:00pm'],
        '8:30pm' => ['slug' => '8:30pm', 'name' => '8:30pm'],
        '9:00pm' => ['slug' => '9:00pm', 'name' => '9:00pm'],
        '9:30pm' => ['slug' => '9:30pm', 'name' => '9:30pm'],
        '10:00pm' => ['slug' => '10:00pm', 'name' => '10:00pm'],
        '10:30pm' => ['slug' => '10:30pm', 'name' => '10:30pm'],
        '11:00pm' => ['slug' => '11:00pm', 'name' => '11:00pm'],
        '11:30pm' => ['slug' => '11:30pm', 'name' => '11:30pm'],
        '12:00pm' => ['slug' => '12:00pm', 'name' => '12:00pm'],
        '12:30pm' => ['slug' => '12:30pm', 'name' => '12:30pm'],
        '1:00pm' => ['slug' => '1:00pm', 'name' => '1:00pm'],
        '1:30pm' => ['slug' => '1:30pm', 'name' => '1:30pm'],
        '2:00pm' => ['slug' => '2:00pm', 'name' => '2:00pm'],
        '2:30pm' => ['slug' => '2:30pm', 'name' => '2:30pm'],
        '3:00pm' => ['slug' => '3:00pm', 'name' => '3:00pm'],
        '3:30pm' => ['slug' => '3:30pm', 'name' => '3:30pm'],
        '4:00pm' => ['slug' => '4:00pm', 'name' => '4:00pm'],
        '4:30pm' => ['slug' => '4:30pm', 'name' => '4:30pm'],
        '5:00pm' => ['slug' => '5:00pm', 'name' => '5:00pm'],
        '5:30pm' => ['slug' => '5:30pm', 'name' => '5:30pm'],
        '6:00pm' => ['slug' => '6:00pm', 'name' => '6:00pm'],
        '6:30pm' => ['slug' => '6:30pm', 'name' => '6:30pm'],
        '7:00pm' => ['slug' => '7:00pm', 'name' => '7:00pm'],
        '7:30pm' => ['slug' => '7:30pm', 'name' => '7:30pm'],
        '8:00pm' => ['slug' => '8:00pm', 'name' => '8:00pm'],
        '8:30pm' => ['slug' => '8:30pm', 'name' => '8:30pm'],
        '9:00pm' => ['slug' => '9:00pm', 'name' => '9:00pm'],
        '9:30pm' => ['slug' => '9:30pm', 'name' => '9:30pm'],
        '10:00pm' => ['slug' => '10:00pm', 'name' => '10:00pm'],
        '10:30pm' => ['slug' => '10:30pm', 'name' => '10:30pm'],
        '11:00pm' => ['slug' => '11:00pm', 'name' => '11:00pm'],
        '11:30pm' => ['slug' => '11:30pm', 'name' => '11:30pm']
    ],

    'day' => [
        '0' => ['slug' => '0', 'name' => 'Sunday'],
        '1' => ['slug' => '1', 'name' => 'Monday'],
        '2' => ['slug' => '2', 'name' => 'Tuesday'],
        '3' => ['slug' => '3', 'name' => 'Wednesday'],
        '4' => ['slug' => '4', 'name' => 'Thursday'],
        '5' => ['slug' => '5', 'name' => 'Friday'],
        '6' => ['slug' => '6', 'name' => 'Saturday'],
    ],

    'employment_type' => [
        'full-time' => ['slug' => 'full-time', 'name' => 'Full Time'],
        'part-time' => ['slug' => 'part-time', 'name' => 'Part Time'],
        'contract' => ['slug' => 'contract', 'name' => 'Contract'],
        'temporary' => ['slug' => 'temporary', 'name' => 'temporary'],
        'volunteer' => ['slug' => 'volunteer', 'name' => 'Volunteer'],
        'internship' => ['slug' => 'internship', 'name' => 'Internship'],
    ],

    'employment_seniority_level' => [
        'internship' => ['slug' => 'internship', 'name' => 'Internship'],
        'entry-level' => ['slug' => 'entry-level', 'name' => 'Entry Level'],
        'associate' => ['slug' => 'associate', 'name' => 'Associate'],
        'mid-senior-level' => ['slug' => 'mid-senior-level', 'name' => 'Mid Senior Level'],
        'director' => ['slug' => 'director', 'name' => 'Director'],
        'executive' => ['slug' => 'executive', 'name' => 'Executive']
    ],

    'business_opportunity_type' => [
        'fundraising' => ['slug' => 'fundraising', 'name' => 'Fundraising'],
        'hiring' => ['slug' => 'hiring', 'name' => 'Hiring'],
        'information' => ['slug' => 'information', 'name' => 'Information'],
        'investment' => ['slug' => 'investment', 'name' => 'Investment'],
        'mentorship' => ['slug' => 'mentorship', 'name' => 'Mentorship'],
        'partnership' => ['slug' => 'partnership', 'name' => 'Partnership'],
        'product' => ['slug' => 'product', 'name' => 'Product'],
        'services' => ['slug' => 'services', 'name' => 'Services']
    ],

    'notification_setting' => [
        'job' =>
            [
                'name' => 'Job Board',
                'active' => 0,
                'list' => [
                    '0' => ['slug' => 0, 'name' => 'Job Recommendations'],
                    '1' => ['slug' => 1, 'name' => 'Insights about talent relevant to your job post']
                ]
            ],
        'business_opportunity' =>
            [
                'name' => 'Business Opportunities',
                'active' => 1,
                'list' => [
                    '2' => ['slug' => 2, 'name' => 'Business Opportunity Recommendations'],
                    '3' => ['slug' => 3, 'name' => 'Insights about members/companies relevant to your business opportunities.']
                ]
            ]

    ],

    'place_action' => [
        '0' => ['slug' => '0', 'name' => 'Track'],
        '1' => ['slug' => '1', 'name' => 'Host']
    ],

    'post_type' => [
        '0' => ['slug' => '0', 'name' => 'Feed'],
        '1' => ['slug' => '1', 'name' => 'Group'],
        '2' => ['slug' => '2', 'name' => 'Event'],
        '3' => ['slug' => '3', 'name' => 'Job Board'],
        '4' => ['slug' => '4', 'name' => 'Business Opportunities']
    ],

    'post_categories' => [
        'adventure' => ['slug' => 'adventure', 'name' => 'Adventure'],
        'business' => ['slug' => 'business', 'name' => 'Business'],
        'discovery' => ['slug' => 'discovery', 'name' => 'Discovery'],
        'education' => ['slug' => 'education', 'name' => 'Education'],
        'fun' => ['slug' => 'fun', 'name' => 'Fun'],
        'fitness' => ['slug' => 'fitness', 'name' => 'Fitness'],
        'networking' => ['slug' => 'networking', 'name' => 'Networking'],
        'social' => ['slug' => 'social', 'name' => 'Social']
    ],

    'post_tags' => [
        'animals and pets' => ['slug' => 'animals and pets', 'name' => 'Animals and Pets'],
        'architecture' => ['slug' => 'architecture', 'name' => 'Architecture'],
        'art' => ['slug' => 'art', 'name' => 'Art'],
        'business' => ['slug' => 'business', 'name' => 'Business'],
        'cars and motorcycles' => ['slug' => 'cars and motorcycles', 'name' => 'Cars and Motorcycles'],
        'celebrations and events' => ['slug' => 'celebrations and events', 'name' => 'Celebrations and Events'],
        'celebrities' => ['slug' => 'celebrities', 'name' => 'Celebrities'],
        'diy and crafts' => ['slug' => 'diy and crafts', 'name' => 'DIY and Crafts'],
        'design' => ['slug' => 'design', 'name' => 'Design'],
        'education' => ['slug' => 'education', 'name' => 'Education'],
        'entertainment' => ['slug' => 'entertainment', 'name' => 'Entertainment'],
        'fun' => ['slug' => 'fun', 'name' => 'Fun'],
        'food and drink' => ['slug' => 'food and drink', 'name' => 'Food and Drink'],
        'gardening' => ['slug' => 'gardening', 'name' => 'Gardening'],
        'hair and beauty' => ['slug' => 'hair and beauty', 'name' => 'Hair and Beauty'],
        'health and fitness' => ['slug' => 'health and fitness', 'name' => 'Health and Fitness'],
        "men's fashion" => ['slug' => "men's fashion", 'name' => "men's fashion"],
        'networking' => ['slug' => 'networking', 'name' => 'Networking'],
        'photography' => ['slug' => 'photography', 'name' => 'Photography'],
        'social' => ['slug' => 'social', 'name' => 'Social'],
        'science and nature' => ['slug' => 'science and nature', 'name' => 'Science and Nature'],
        'sports' => ['slug' => 'sports', 'name' => 'Sports'],
        'tattoos' => ['slug' => 'tattoos', 'name' => 'Tattoos'],
        'technology' => ['slug' => 'technology', 'name' => 'Technology'],
        'travel' => ['slug' => 'travel', 'name' => 'Travel'],
        'weddings' => ['slug' => 'weddings', 'name' => 'Weddings'],
        "women's fashion" => ['slug' => "women's fashion", 'name' => "women's fashion"],
    ],

    'activity_type' => [
        '0' => ['slug' => '0', 'name' => 'Follow', 'setting' => ['show' => 1, 'notification' => 1]],
        '1' => ['slug' => '1', 'name' => 'Unfollow', 'setting' => ['show' => 0, 'notification' => 0]],
        '2' => ['slug' => '2', 'name' => 'Like', 'setting' => ['show' => 1, 'notification' => 1]],
        '3' => ['slug' => '3', 'name' => 'UnLike', 'setting' => ['show' => 0, 'notification' => 0]],
        '4' => ['slug' => '4', 'name' => 'Join', 'setting' => ['show' => 1, 'notification' => 1]],
        '5' => ['slug' => '5', 'name' => 'Leave', 'setting' => ['show' => 0, 'notification' => 0]],
        '6' => ['slug' => '6', 'name' => 'Going', 'setting' => ['show' => 1, 'notification' => 1]],
        '7' => ['slug' => '7', 'name' => 'No Going', 'setting' => ['show' => 0, 'notification' => 0]],
        '8' => ['slug' => '8', 'name' => 'New Post', 'setting' => ['show' => 1, 'notification' => 1]],
        '9' => ['slug' => '9', 'name' => 'New Group', 'setting' => ['show' => 1, 'notification' => 1]],
        '10' => ['slug' => '10', 'name' => 'New Event', 'setting' => ['show' => 1, 'notification' => 1]],
        '11' => ['slug' => '11', 'name' => 'New Comment', 'setting' => ['show' => 1, 'notification' => 1]],
        '12' => ['slug' => '12', 'name' => 'Mention', 'setting' => ['show' => 1, 'notification' => 1]],
        '13' => ['slug' => '13', 'name' => 'Invite', 'setting' => ['show' => 1, 'notification' => 1]],
        '14' => ['slug' => '14', 'name' => 'New Group Post', 'setting' => ['show' => 1, 'notification' => 1]],
        '15' => ['slug' => '15', 'name' => 'Work', 'setting' => ['show' => 1, 'notification' => 1]],
        '16' => ['slug' => '16', 'name' => 'New Event Post', 'setting' => ['show' => 1, 'notification' => 1]],
        '17' => ['slug' => '17', 'name' => 'New Job', 'setting' => ['show' => 1, 'notification' => 0]],
        '18' => ['slug' => '18', 'name' => 'New Job Post', 'setting' => ['show' => 1, 'notification' => 0]],
        '19' => ['slug' => '19', 'name' => 'Job Recommendations', 'setting' => ['show' => 0, 'notification' => 1]],
        '20' => ['slug' => '20', 'name' => 'Insights about Talent Relevant to Your Job Post', 'setting' => ['show' => 0, 'notification' => 1]],
        '21' => ['slug' => '21', 'name' => 'Apply', 'setting' => ['show' => 1, 'notification' => 1]],
        '22' => ['slug' => '22', 'name' => 'Employ', 'setting' => ['show' => 1, 'notification' => 1]],

        '23' => ['slug' => '23', 'name' => 'New Business Opportunity', 'setting' => ['show' => 1, 'notification' => 0]],
        '24' => ['slug' => '24', 'name' => 'New Business Opportunity Post', 'setting' => ['show' => 1, 'notification' => 0]],
        '25' => ['slug' => '25', 'name' => 'Business Opportunity Recommendations', 'setting' => ['show' => 0, 'notification' => 1]],
        '26' => ['slug' => '26', 'name' => 'Insights about Members/Companies Relevant to Your Business Opportunities', 'setting' => ['show' => 0, 'notification' => 1]]
    ],

    'like_text' => [
        '0' => ['slug' => '0', 'name' => '%s %s'],
        '1' => ['slug' => '1', 'name' => 'You like this'],
        '2' => ['slug' => '2', 'name' => 'You and %s others like this']
    ],

    'comment_text' => [
        '0' => ['slug' => '0', 'name' => '%s %s'],
        '1' => ['slug' => '1', 'name' => 'You comment this'],
        '2' => ['slug' => '2', 'name' => 'You and %s others comment this']
    ],

    'join_text' => [
        '0' => ['slug' => '0', 'name' => '%s %s'],
        '1' => ['slug' => '1', 'name' => 'You are joining'],
        '2' => ['slug' => '2', 'name' => 'You and %s others joining']
    ],

    'going_text' => [
        '0' => ['slug' => '0', 'name' => '%s %s'],
        '1' => ['slug' => '1', 'name' => 'You are going'],
        '2' => ['slug' => '2', 'name' => 'You and %s others going']
    ],

    'apply_text' => [
        '0' => ['slug' => '0', 'name' => '%s %s'],
        '1' => ['slug' => '1', 'name' => 'You are applying'],
        '2' => ['slug' => '2', 'name' => 'You and %s others apply']
    ],

    'employ_text' => [
        '0' => ['slug' => '0', 'name' => '%s %s'],
        '1' => ['slug' => '1', 'name' => 'You are employed'],
        '2' => ['slug' => '2', 'name' => 'You and %s others employed']
    ],

    'feed_master_filter_menu' => [
        'others' => ['slug' => 'others', 'name' => 'Others'],
    ],

    'territories_menu' => [
        'others' => ['slug' => 'others', 'name' => 'Others'],
    ],

    /*
    |--------------------------------
    | Default commission structures
    |---------------------------------
    |
    */
    'commission_structure' => [
        'agent' => [
            [
                'percentage' => 10,
                'type' => 2,
                'type_number' => 1,
                'min' => 0,
                'max' => 12,
            ],
            [
                'percentage' => 2,
                'type' => 2,
                'type_number' => 2,
                'min' => 12,
                'max' => 0,
            ],
        ],
        'user' => [
            [
                'percentage' => 5,
                'type' => 1,
                'type_number' => 1,
                'min' => 1500,
                'max' => 6500,
            ],
            [
                'percentage' => 6,
                'type' => 1,
                'type_number' => 2,
                'min' => 6501,
                'max' => 8500,
            ],
            [
                'percentage' => 7,
                'type' => 1,
                'type_number' => 3,
                'min' => 8501,
                'max' => 24000,
            ],
            [
                'percentage' => 8,
                'type' => 1,
                'type_number' => 4,
                'min' => 24001,
                'max' => 0,
            ]
        ],
        'salesperson' => [
            [
                'percentage' => 10,
                'type' => 2,
                'type_number' => 1,
                'min' => 0,
                'max' => 12,
            ],
            [
                'percentage' => 15,
                'type' => 2,
                'type_number' => 2,
                'min' => 12,
                'max' => 0,
            ]
        ]
    ],

    /*
    |----------------------------------------------------------
    | Type of commission being used inside commission structure
    |----------------------------------------------------------
    |
    */
    'commission_type' => [
        '0' => ['slug' => '0', 'name' => 'direct'],
        '1' => ['slug' => '1', 'name' => 'tier'],
        '2' => ['slug' => '2', 'name' => 'contract']
    ],

    /*
    |----------------------------------------------------------
    | Commissions roles
    |----------------------------------------------------------
    |
    */
    'commission_schema' => [
        'agent' => ['slug' => 'agent', 'name' => 'Agent'],
        'user' => ['slug' => 'user', 'name' => 'Member'],
        'salesperson' => ['slug' => 'salesperson', 'name' => 'Salesperson']
    ],
	
	'lead_source' => [
		'admin' => ['slug' => 'admin', 'name' => 'Admin Portal'],
		'agent' => ['slug' => 'agent', 'name' => 'Agent Portal'],
		'member' => ['slug' => 'member', 'name' => 'Member Portal'],
		'website' => ['slug' => 'website', 'name' => 'Website']
	],
	
	'lead_status' => [
		'lead' => ['slug' => 'lead', 'name' => 'Lead'],
		'booking' => ['slug' => 'booking', 'name' => 'Tour Booked'],
		'tour' => ['slug' => 'tour', 'name' => 'Toured'],
		'follow-up' => ['slug' => 'follow-up', 'name' => 'Follow Up'],
		'win' => ['slug' => 'win', 'name' => 'Won'],
		'lost' => ['slug' => 'lost', 'name' => 'Lost']
	]
	
];