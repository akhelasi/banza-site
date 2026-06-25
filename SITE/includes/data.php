<?php

declare(strict_types=1);

require_once __DIR__ . '/content-store.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/weather.php';
require_once __DIR__ . '/repositories/page-repository.php';
require_once __DIR__ . '/repositories/post-repository.php';
require_once __DIR__ . '/repositories/settings-repository.php';

$site = [
    'title' => 'ბანძა',
    'tagline' => 'ჩვენი სახლი, ჩვენი ისტორია',
    'description' => 'სოფლის ამბები, ისტორია, პროექტები და საერთო საქმეები ერთ სივრცეში.',
    'hero_image' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/St.%20Virgin%20church%20of%20Bandza%20Angle.jpg',
];

$navigation = [
    ['label' => 'მთავარი', 'href' => 'index.php', 'key' => 'home'],
    ['label' => 'ახალი ამბები', 'href' => 'news.php', 'key' => 'news'],
    ['label' => 'ისტორია', 'href' => 'history.php', 'key' => 'history'],
    ['label' => 'პროექტები', 'href' => 'projects.php', 'key' => 'projects'],
    ['label' => 'ჩვენ შესახებ', 'href' => 'about.php', 'key' => 'about'],
    ['label' => 'კონტაქტი', 'href' => 'contact.php', 'key' => 'contact'],
];

$socialLinks = [
    ['label' => 'Facebook', 'icon' => 'f', 'href' => 'https://facebook.com/'],
    ['label' => 'Instagram', 'icon' => '◎', 'href' => 'https://instagram.com/'],
    ['label' => 'YouTube', 'icon' => '▶', 'href' => 'https://youtube.com/'],
];

$weather = [
    'summary' => 'ნაწილობრივ ღრუბლიანი',
    'temperature' => '18°C',
    'wind' => '8 კმ/სთ',
    'humidity' => '65%',
    'rain' => '20%',
    'nearby' => [
        ['name' => 'მარტვილი', 'forecast' => 'მზიანი', 'temperature' => '21°C'],
        ['name' => 'აბაშა', 'forecast' => 'ღრუბლიანი', 'temperature' => '20°C'],
        ['name' => 'გაჭედილი', 'forecast' => 'მცირე წვიმა', 'temperature' => '19°C'],
    ],
];

$camera = [
    'title' => 'ლაივ კამერა',
    'status' => 'LIVE demo',
    'preview_image' => $site['hero_image'],
    'stream_url' => '',
    'description' => 'კამერის რეალური stream მისამართი დაემატება სოფელში მოწყობილობის დაყენების შემდეგ.',
];

$bankAccounts = [
    ['bank' => 'საქართველოს ბანკი', 'account' => 'GE00BG0000000000000000', 'note' => 'Demo ანგარიში'],
    ['bank' => 'თიბისი ბანკი', 'account' => 'GE00TB0000000000000000', 'note' => 'Demo ანგარიში'],
];

$projects = [
    [
        'slug' => 'village-development-roadmap',
        'title' => 'სოფლის განვითარების გზამკვლევი',
        'excerpt' => 'საერთო საჭიროებების ჩამონათვალი და პრიორიტეტები ინფრასტრუქტურისთვის.',
        'source_status' => 'demo',
        'source_note' => 'Demo project record; replace with client-approved project details.',
        'body' => 'პროექტის მიზანია სოფლის საჭიროებების ერთიანად აღწერა: გზები, განათება, წყალი, საზოგადოებრივი სივრცეები და ტურისტული ნიშნულები. ამ ეტაპზე ჩანაწერი demo სტატუსშია და შემდეგ კლიენტის მონაცემებით შეივსება.',
        'status' => 'დაგეგმილი',
        'category' => 'ინფრასტრუქტურა',
        'image' => $site['hero_image'],
        'featured' => true,
    ],
    [
        'slug' => 'youth-space',
        'title' => 'ახალგაზრდული სივრცე',
        'excerpt' => 'სასწავლო, სპორტული და კულტურული აქტივობებისთვის სივრცის მოწყობა.',
        'source_status' => 'demo',
        'source_note' => 'Demo project record; replace with client-approved project details.',
        'body' => 'სივრცე დაეხმარება ახალგაზრდებს შეხვედრების, მცირე ღონისძიებების, სწავლებისა და სპორტული ინიციატივების ორგანიზებაში.',
        'status' => 'იდეა',
        'category' => 'საზოგადოება',
        'image' => asset('images/football-team.png'),
        'featured' => true,
    ],
    [
        'slug' => 'eco-tourism-route',
        'title' => 'ეკო ტურიზმის მარშრუტი',
        'excerpt' => 'ბანძის ისტორიული და ბუნებრივი წერტილების ერთიანი მარშრუტი სტუმრებისთვის.',
        'source_status' => 'demo',
        'source_note' => 'Demo project record; replace with client-approved project details.',
        'body' => 'მარშრუტი შეიძლება აერთიანებდეს ისტორიულ ობიექტებს, სოფლის ხედებს, ადგილობრივ ისტორიებს და სტუმრისთვის საჭირო მოკლე ცნობებს.',
        'status' => 'კვლევა',
        'category' => 'ტურიზმი',
        'image' => $site['hero_image'],
        'featured' => true,
    ],
    [
        'slug' => 'development-fund-transparency',
        'title' => 'განვითარების ფონდის გამჭვირვალობა',
        'excerpt' => 'შემოწირულობების, ანგარიშებისა და შესრულებული საქმეების საჯარო აღრიცხვა.',
        'source_status' => 'demo',
        'source_note' => 'Demo project record; replace with real fund/account details.',
        'body' => 'ფონდის გვერდი მომავალში აჩვენებს ანგარიშებს, შემოსულ მხარდაჭერას და დასრულებულ საქმეებს.',
        'status' => 'მომზადება',
        'category' => 'ფონდი',
        'image' => asset('images/donation-fund.png'),
        'featured' => false,
    ],
];

$news = [
    [
        'slug' => 'banza-site-first-version',
        'date' => '20 ივნისი',
        'published_at' => '2026-06-20',
        'title' => 'ბანძის ახალი საიტის პირველადი ვერსია მზადდება',
        'excerpt' => 'საიტი გააერთიანებს სოფლის ამბებს, პროექტებს, ისტორიას და საზოგადოებრივ ინფორმაციას.',
        'source_status' => 'demo',
        'source_note' => 'Demo news record for layout and QA.',
        'body' => [
            'პირველი ვერსია აწყობს სოფლის საიტის ძირითად ჩარჩოს: მთავარ გვერდს, სიახლეებს, პროექტებს, ისტორიას და საკონტაქტო სივრცეს.',
            'შემდეგ ეტაპებზე დაემატება admin panel, საიდანაც შესაძლებელი იქნება სიახლეების, ფოტოებისა და გვერდების კონტენტის მართვა.',
        ],
        'image' => $site['hero_image'],
        'category' => 'სოფლის ამბები',
        'gallery' => [$site['hero_image'], asset('images/donation-fund.png')],
        'videos' => [
            ['title' => 'Demo ვიდეო', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ],
    ],
    [
        'slug' => 'development-fund-page',
        'date' => '18 ივნისი',
        'published_at' => '2026-06-18',
        'title' => 'განვითარების ფონდის გვერდი დაემატება',
        'excerpt' => 'შემოწირულობის ანგარიშები და ფონდის ინფორმაცია admin panel-იდან განახლდება.',
        'source_status' => 'demo',
        'source_note' => 'Demo news record; donation details need client approval.',
        'body' => [
            'დონაციის გვერდის მიზანია სოფლის მხარდამჭერებმა მარტივად ნახონ საბანკო ანგარიშები და ფონდის მიმდინარე საჭიროებები.',
            'ამ ეტაპზე ანგარიშები demo მონაცემებია. რეალური მონაცემები დაემატება კლიენტისგან მიღების შემდეგ.',
        ],
        'image' => asset('images/donation-fund.png'),
        'category' => 'ფონდი',
        'gallery' => [asset('images/donation-fund.png')],
        'videos' => [],
    ],
    [
        'slug' => 'fc-ojaleshi-bandza-page',
        'date' => '15 ივნისი',
        'published_at' => '2026-06-15',
        'title' => 'FC ოჯალეში ბანძას გვერდი მზადდება',
        'excerpt' => 'ფეხბურთის გუნდისთვის ცალკე გვერდი შეიქმნება ფოტოებით, ტექსტებით და სიახლეებით.',
        'source_status' => 'demo',
        'source_note' => 'Demo football news record; replace with team-approved content.',
        'body' => [
            'გუნდის გვერდი გააერთიანებს ფოტოს, მოკლე ისტორიას, თამაშების ამბებს და გულშემატკივრებისთვის საჭირო ინფორმაციას.',
            'კონტენტი ამ ეტაპზე საწყისია და შემდეგ admin panel-იდან განახლდება.',
        ],
        'image' => asset('images/football-team.png'),
        'category' => 'სპორტი',
        'gallery' => [asset('images/football-team.png')],
        'videos' => [],
    ],
    [
        'slug' => 'history-archive-seed',
        'date' => '12 ივნისი',
        'published_at' => '2026-06-12',
        'title' => 'ისტორიული ცნობების არქივის მომზადება იწყება',
        'excerpt' => 'საიტზე ბანძის ისტორიის გვერდი ადგილობრივი ფოტოებით და ზეპირი ისტორიებით შეივსება.',
        'source_status' => 'researched',
        'source_note' => 'Seed history note based on public references listed in SITE/content-sources.md.',
        'body' => [
            'ბანძა წყაროებში XVII საუკუნიდან ჩანს. საიტის მიზანია საჯარო წყაროების გარდა ადგილობრივი მოგონებების და ფოტოების შეგროვებაც.',
            'ეს ჩანაწერი demo მაგალითია, რომელიც მომავალში რეალური მასალით შეიცვლება.',
        ],
        'image' => $site['hero_image'],
        'category' => 'ისტორია',
        'gallery' => [$site['hero_image']],
        'videos' => [],
    ],
];

$about = [
    'title' => 'ჩვენ შესახებ',
    'excerpt' => 'ბანძა არის სოფელი მარტვილის მუნიციპალიტეტში, სამეგრელო-ზემო სვანეთის მხარეში. სოფელი მდებარეობს მდინარე აბაშის მარცხენა სანაპიროზე და აერთიანებს ადგილობრივ ისტორიას, მიწასთან კავშირს და საერთო განვითარების სურვილს.',
    'source_status' => 'researched',
    'source_note' => 'Public seed facts from Wikipedia/Geostat references; households and vineyard figures are demo and must be verified with the client.',
    'body' => [
        'ბანძის საიტი იქმნება როგორც სოფლის საერთო საინფორმაციო სივრცე. აქ განთავსდება ახალი ამბები, განვითარების პროექტები, ისტორიული ცნობები, ფოტოები და საკონტაქტო ინფორმაცია.',
        'საწყისი ცნობების ნაწილი ეყრდნობა საჯარო წყაროებს, ხოლო მოსახლეობის, ოჯახებისა და ვენახების დეტალური მონაცემები კლიენტთან გადამოწმდება.',
    ],
    'stats' => [
        ['value' => '1099', 'label' => 'მოსახლე', 'note' => '2014 აღწერა'],
        ['value' => '350+', 'label' => 'ოჯახი', 'note' => 'Demo'],
        ['value' => '500+ ჰა', 'label' => 'ვენახი', 'note' => 'გადასამოწმებელი'],
        ['value' => '90 მ', 'label' => 'ზღვის დონიდან', 'note' => 'წყაროებში მითითებული'],
    ],
];

$history = [
    'title' => 'ისტორია',
    'excerpt' => 'ბანძა წყაროებში XVII საუკუნიდან ჩანს. ისტორიულ ტექსტებში სოფელი უკავშირდება დასავლეთ საქართველოს მნიშვნელოვან მოვლენებს, ძველ ციხე-სასახლეს, ეკლესიას და ადგილობრივ ტრადიციებს.',
    'source_status' => 'researched',
    'source_note' => 'Public seed facts from references listed in SITE/content-sources.md; final historical text needs client/local review.',
    'body' => [
        'საჯარო წყაროების მიხედვით ბანძა პირველად მოხსენიებულია 1639-1640 წლებში. სოფლის ისტორიული კონტექსტი დაკავშირებულია სამეგრელოსა და დასავლეთ საქართველოს წარსულთან.',
        'წყაროებში გვხვდება ბანძის ღვთისმშობლის მიძინების ტაძარი, ბანძის სინაგოგა და ისტორიული ციხე-სასახლე. ამ გვერდს მომავალში დაემატება ადგილობრივი ფოტოები, ზეპირი ისტორიები და არქივიდან მოპოვებული მასალა.',
    ],
    'detail' => 'საწყისი ტექსტი შედგენილია საჯარო წყაროებზე დაყრდნობით და მომავალში უნდა შეივსოს ადგილობრივი არქივებით, ფოტოებით და ზეპირი ისტორიებით.',
];

$football = [
    'title' => 'FC ოჯალეში ბანძა',
    'excerpt' => 'სოფლის საფეხბურთო გუნდის გვერდი გააერთიანებს გუნდის ისტორიას, ფოტოებს, თამაშების ამბებს და გულშემატკივრების ინფორმაციას.',
    'source_status' => 'demo',
    'source_note' => 'Demo football content; replace with team-approved text, gallery and match details.',
    'body' => [
        'FC ოჯალეში ბანძას გვერდი განკუთვნილია გუნდის ამბებისთვის, მატჩების სიახლეებისთვის, ფოტოებისა და სოფლის სპორტული ცხოვრების წარმოსაჩენად.',
        'ამ ეტაპზე გამოყენებულია ატვირთული ვიზუალური მასალა. შემდეგ ფაზებში admin panel-იდან შესაძლებელი იქნება გუნდის ტექსტების, გალერეისა და ვიდეოების მართვა.',
    ],
    'image' => asset('images/football-team.png'),
];

$contact = [
    'title' => 'კონტაქტი',
    'excerpt' => 'დაგვიკავშირდით სოფლის ამბების, პროექტების, ფოტოების ან განვითარების ფონდის საკითხებზე.',
    'source_status' => 'demo',
    'source_note' => 'Demo contact details; replace with real email, phone and location notes before launch.',
    'items' => [
        ['label' => 'ელფოსტა', 'value' => 'info@banza.ge', 'note' => 'Demo მისამართი'],
        ['label' => 'ტელეფონი', 'value' => '+995 000 00 00 00', 'note' => 'Demo ნომერი'],
        ['label' => 'ლოკაცია', 'value' => 'ბანძა, მარტვილის მუნიციპალიტეტი', 'note' => 'სამეგრელო-ზემო სვანეთი'],
    ],
];
$notifications = [
    'enabled' => false,
    'recipient_email' => '',
    'from_email' => '',
    'subject_prefix' => '[Banza Site]',
];
$seedContent = [
    'news' => $news,
    'projects' => $projects,
    'about' => $about,
    'history' => $history,
    'football' => $football,
    'contact' => $contact,
    'socialLinks' => $socialLinks,
    'bankAccounts' => $bankAccounts,
    'camera' => $camera,
    'weather' => $weather,
    'notifications' => $notifications,
    'mediaItems' => [],
    'contactMessages' => [],
];

$contentStore = load_content_store($seedContent);
if (content_storage_driver() === 'mysql') {
    try {
        $mysqlSettings = load_runtime_settings_from_mysql(db());
        $contentStore = array_replace($contentStore, array_filter(
            $mysqlSettings,
            static fn (mixed $value): bool => is_array($value) && $value !== []
        ));
        $mysqlPages = load_runtime_pages_from_mysql(db());
        $contentStore = array_replace($contentStore, array_filter(
            $mysqlPages,
            static fn (mixed $value): bool => is_array($value) && $value !== []
        ));
        $mysqlPosts = load_runtime_posts_from_mysql(db());
        $contentStore = array_replace($contentStore, array_filter(
            $mysqlPosts,
            static fn (mixed $value): bool => is_array($value) && $value !== []
        ));
    } catch (Throwable $exception) {
        error_log('MySQL runtime content fallback to JSON: ' . $exception->getMessage());
    }
}
$news = visible_content_items($contentStore['news'] ?? []);
$projects = visible_content_items($contentStore['projects'] ?? []);
$about = $contentStore['about'] ?? $about;
$history = $contentStore['history'] ?? $history;
$football = $contentStore['football'] ?? $football;
$contact = $contentStore['contact'] ?? $contact;
$socialLinks = visible_content_items($contentStore['socialLinks'] ?? []);
$bankAccounts = visible_content_items($contentStore['bankAccounts'] ?? []);
$camera = $contentStore['camera'] ?? $camera;
$weather = resolve_weather_data($contentStore['weather'] ?? $weather);
$notifications = $contentStore['notifications'] ?? $notifications;
$mediaItems = $contentStore['mediaItems'] ?? [];
$contactMessages = $contentStore['contactMessages'] ?? [];
