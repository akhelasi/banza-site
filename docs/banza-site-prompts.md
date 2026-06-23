# Banza Site Prompts

This file keeps the agreed project prompts so future Codex runs can continue from the same brief.

## Full Project Prompt

იმუშავე ამ repo-ში AGENTS.md-ის მიხედვით.

პროექტი:
`D:\avto\CODEX-ით მუშაობა\სოფლის ( ბანძა ) საიტი`

რეალური საიტის კოდი არის `SITE/` ფოლდერში. Starter pack ფაილები არ შეცვალო, თუ აუცილებელი არ არის.

მინდა ააშენო სოფელ ბანძის ვებსაიტი ქართულ ენაზე. საიტის მთავარი მიზანია ახალი ამბების გამოქვეყნება, მაგრამ ასევე უნდა ჰქონდეს სტატიკური/იშვიათად შესაცვლელი გვერდები: ისტორია, პროექტები, ჩვენ შესახებ, კონტაქტი, ფეხბურთის გუნდის გვერდი და სხვა საჭირო კონტენტი.

მნიშვნელოვანი: 0 კონტენტით არ იმუშავო. სანამ frontend/backend-ს ააწყობ, მოიძიე ინტერნეტში სოფელ "ბანძა"-ზე საწყისი ინფორმაცია და გამოიყენე seed/demo content-ისთვის.

კვლევის წესები:
- გამოიყენე ინტერნეტი სოფელ ბანძაზე ინფორმაციის მოსაძიებლად.
- გადაამოწმე წყაროები: ოფიციალური/სანდო წყაროები პრიორიტეტულია.
- არ დააკოპირო დიდი ტექსტები სიტყვასიტყვით. გადაამუშავე მოკლე, ბუნებრივ ქართულ ტექსტად.
- თუ მონაცემი დაუზუსტებელია, admin/content-ში ჩაწერე როგორც draft/demo ან მიუთითე, რომ გადასამოწმებელია.
- შეინახე გამოყენებული წყაროების სია, მაგალითად `SITE/content-sources.md` ფაილში ან seed notes-ში.
- მოძებნე ინფორმაცია მინიმუმ ამ თემებზე:
  - სოფლის ზოგადი აღწერა.
  - ისტორიული ცნობები, თუ ხელმისაწვდომია.
  - მდებარეობა/მუნიციპალიტეტი/რეგიონი.
  - მოსახლეობა, თუ სანდო წყარო არსებობს.
  - ადგილობრივი ღირსშესანიშნაობები ან მნიშვნელოვანი ობიექტები.
  - სოფლისთვის შესაფერისი news/project demo თემები.
- თუ ზუსტი ინფორმაცია ვერ მოიძებნა, შექმენი მკაფიოდ demo content და არ წარმოაჩინო როგორც დადასტურებული ფაქტი.

გამოიყენე ატვირთული 3 სურათი:
1. მთავარი გვერდის დიზაინის reference სურათი - არ დააკოპირო ერთი-ერთში, მაგრამ აიღე სტილი, განწყობა, ფერები, განლაგების იდეა.
2. ბანძას ფეხბურთის გუნდის სურათი - გამოიყენე მთავარ გვერდზე ფეხბურთის გუნდის ბლოკში და გუნდის შიდა გვერდზე.
3. "ბანძას განვითარების ფონდი" სურათი - გამოიყენე donation/sidebar ბლოკში.

ტექნიკური მოთხოვნები:
- გამოიყენე არსებული PHP scaffold, plain PHP, HTML, CSS, JavaScript და MySQL/MariaDB.
- არ დაამატო მძიმე framework, თუ აუცილებელი არ არის.
- კოდი იყოს უსაფრთხო: prepared statements, output escaping, admin auth, CSRF დაცვა forms-ზე, ფაილების upload validation.
- სურათები upload-ისას შეინახოს `SITE/uploads/` ფოლდერში ან მსგავსი მკაფიო სტრუქტურით.
- წაშლა იყოს soft delete: წაშლილი კონტენტი/ფაილები გადავიდეს "სანაგვე"-ში, საიდანაც შესაძლებელი იქნება restore ან permanent delete.
- admin panel-იდან უნდა ხდებოდეს კონტენტის დამატება/რედაქტირება/წაშლა.
- დიზაინი იყოს responsive, mobile-friendly, ქართულ შრიფტზე მორგებული, სუფთა და თანამედროვე.

ფაზები:

ფაზა 0: კვლევა და seed content
- ინტერნეტში მოიძიე სოფელ ბანძაზე ინფორმაცია.
- შეადგინე საწყისი ქართული კონტენტი:
  - მთავარი გვერდის hero ტექსტი.
  - "ჩვენ შესახებ" მოკლე და სრული ტექსტი.
  - "ისტორია" მოკლე და სრული ტექსტი.
  - 3 demo ახალი ამბავი.
  - 3 demo პროექტი.
  - ფეხბურთის გუნდის გვერდის საწყისი ტექსტი.
  - კონტაქტის demo ტექსტი.
  - donation/demo bank account placeholder-ები.
- წყაროები შეინახე ცალკე ფაილში.
- seed content გამოიყენე საიტში ისე, რომ UI რეალური კონტენტით შემოწმდეს.

ფაზა 1: საძირკველი
- `SITE/` სტრუქტურის დალაგება.
- საერთო layout: header, footer, responsive grid.
- CSS design system: ფერები, spacing, cards, buttons, modal styles.
- uploaded სურათების პროექტში გადმოტანა.
- `.gitignore`-ში `schema.sql` პრობლემის გასწორება, რადგან ახლა `*.sql` იგნორდება.
- DB connection helper.
- საერთო PHP helper-ები: escaping, redirect, CSRF, auth/session basics.
- საწყისი `schema.sql`.
- seed/demo data სტრუქტურის მომზადება.

ფაზა 2: Public მთავარი გვერდი
- Header:
  - მარცხნივ ბანძას ლოგო/სახელი.
  - შუაში navigation: მთავარი, ახალი ამბები, ისტორია, პროექტები, ჩვენ შესახებ, კონტაქტი.
  - მარჯვნივ social icon-ები: Facebook, Instagram, YouTube. ლინკები იმართებოდეს admin panel-იდან.
- Hero section:
  - ფონად სოფლის/მთის მსგავსი სურათი, reference-ის სტილში.
  - მარცხნივ დიდი ქართული სათაური და მოკლე ტექსტი.
  - მარჯვნივ ორი ფანჯარა:
    1. Live camera card - დროებით placeholder/preview, admin-იდან სამართავი stream/embed URL-ით. ღილაკზე დაკლიკვისას გაიხსნას modal, სადაც კამერა უფრო დიდად ჩანს.
    2. Live weather card - ბანძის ამინდის მოკლე პროგნოზი. დაკლიკვისას გაიხსნას modal დეტალური პროგნოზით და ახლომდებარე სოფლების ამინდით. თუ რეალური API ჯერ არ არის მზად, გააკეთე mock/configurable სტრუქტურა.
- Hero-ს ქვემოთ main content + right sidebar layout:
  - Sidebar:
    1. Donation card "ბანძას განვითარების ფონდი" სურათით.
       - ღილაკზე იხსნება modal ბანკების ანგარიშის ნომრებით.
       - ანგარიშების დამატება/წაშლა/რედაქტირება ხდებოდეს admin panel-იდან.
    2. "პოპულარული პროექტები" card - აჩვენოს 3 ყველაზე პოპულარული/გამორჩეული პროექტი.
    3. "გამოგვყევი" card - social links icon-ებით, admin-იდან სამართავი.
  - Main content:
    1. ფეხბურთის გუნდის feature card:
       - გამოიყენე ატვირთული გუნდის სურათი.
       - მოკლე აღწერა.
       - card-ზე დაკლიკვით გადადიოდეს ფეხბურთის გუნდის შიდა გვერდზე.
       - გუნდის გვერდის კონტენტი admin-იდან იმართებოდეს.
    2. ბოლო 3 ახალი ამბავი card-ებად.
    3. "ჩვენ შესახებ" preview: სურათი თუ არსებობს, მოსახლეობა, ოჯახების რაოდენობა, ვენახების ფართობი, ზღვის დონიდან სიმაღლე და მოკლე ტექსტი.
    4. "ისტორია" preview: სურათი თუ არსებობს და მოკლე აღწერა.

ფაზა 3: Public გვერდები
- ყველა გვერდზე header-ის ქვეშ იყოს page hero:
  - გვერდის სათაური.
  - მოკლე აღწერა.
  - შესაბამისი ფონური სურათი.
- გვერდები:
  1. ახალი ამბები
  2. ისტორია
  3. პროექტები
  4. ჩვენ შესახებ
  5. კონტაქტი
  6. ფეხბურთის გუნდი / FC ოჯალეში ბანძა
- "ახალი ამბები" გვერდზე:
  - search და filters live რეჟიმში, page reload-ის გარეშე.
  - scroll არ ავარდეს ზემოთ filter/search ცვლილებისას.
  - cards: მთავარი სურათი, სათაური, მოკლე აღწერა, თარიღი.
  - card-ზე დაკლიკებით იხსნებოდეს news detail გვერდი.
- News detail გვერდზე:
  - მთავარი სურათი, სათაური, სრული ტექსტი, თარიღი.
  - სურათების გალერეა admin upload-ით.
  - სურათზე დაკლიკებისას modal/lightbox, სადაც სურათი ჩანს სრულად და არ იჭრება.
  - YouTube video links admin-იდან; ვიდეოზე დაკლიკებისას modal-ში embed player.
- დანარჩენ გვერდებზე:
  - search/filter სადაც ლოგიკურია.
  - მთავარი კონტენტი მოდიოდეს admin panel-იდან.

ფაზა 4: Admin Panel Skeleton
- admin login/logout.
- session დაცვა.
- CSRF დაცვა.
- admin dashboard.
- admin navigation.
- CRUD screens-ის საწყისი სტრუქტურა.
- flash messages: success/error.
- basic authorization.

ფაზა 5: Content CRUD
Admin panel-იდან მართვადი იყოს:
- ახალი ამბები.
- პროექტები.
- ისტორიის გვერდის კონტენტი.
- ჩვენ შესახებ გვერდის კონტენტი და სტატისტიკები:
  - მოსახლეობა.
  - ოჯახების რაოდენობა.
  - ვენახების ფართობი.
  - ზღვის დონიდან სიმაღლე.
- ფეხბურთის გუნდის გვერდის კონტენტი.
- კონტაქტის ინფორმაცია.
- social links.
- donation bank accounts.
- live camera config.
- weather/settings placeholder/config.
- page hero texts/images.

ფაზა 6: Uploads, Gallery, Videos
- image upload validation.
- upload folder structure.
- news main image.
- news gallery images.
- gallery lightbox/modal.
- YouTube video links.
- video modal/embed.
- uploaded files admin view.
- ფაილის გამოყენების შემოწმება წაშლამდე.

ფაზა 7: Trash / Soft Delete
- ყველა მნიშვნელოვანი კონტენტის soft delete.
- admin trash page.
- restore.
- permanent delete.
- permanent delete-ზე ფაილის რეალური წაშლა მხოლოდ მაშინ, თუ სხვაგან აღარ გამოიყენება.
- DB queries-ში `deleted_at IS NULL` წესის დაცვა.

ფაზა 8: QA, Polish, Security Review
- `php -l` ყველა შეცვლილ PHP ფაილზე.
- public flows:
  - მთავარი გვერდი.
  - news list search/filter.
  - news detail.
  - gallery modal.
  - donation modal.
  - camera/weather modal.
  - responsive mobile.
- admin flows:
  - login/logout.
  - create/edit/delete/restore.
  - image upload.
- browser QA desktop/mobile.
- security pass:
  - prepared statements.
  - escaping.
  - CSRF.
  - upload validation.
  - admin-only routes.
- visual polish.

## Phase 0-2 Implementation Prompt

პირველი implementation ეტაპისთვის გააკეთე მხოლოდ:
- ფაზა 0
- ფაზა 1
- ფაზა 2

ანუ ჯერ მოიძიე კონტენტი, მოამზადე საძირკველი და ააწყე მთავარი გვერდი რეალური/seed კონტენტით. Admin panel-ის სრული CRUD ჯერ არ გააკეთო, მაგრამ schema/helpers ისე დაგეგმე, რომ შემდეგ ფაზებში ადვილად გაგრძელდეს.

ბოლოს მომეცი მოკლე summary:
- რა გაკეთდა.
- რომელი ფაილები შეიცვალა.
- რა წყაროები გამოიყენე კონტენტისთვის.
- რა შემოწმდა.
- რა დარჩა შემდეგ ფაზაში.
