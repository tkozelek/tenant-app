<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\GlobalProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GlobalProductSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Illuminate\Support\Collection<string, Attribute> $attrs */
        $attrs = Attribute::with('attributeValues')->get()->keyBy('slug');

        $products = [

            // ── Apple iPhone 15 ──────────────────────────────────────────────────
            [
                'name' => 'Apple iPhone 15 128 GB',
                'description' => 'iPhone 15 s 6,1" Super Retina XDR displejom, čipom A16 Bionic, dynamickým ostrovom a 48 MP hlavným fotoaparátom. Interná pamäť 128 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '128 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Full HD',
                    'uhlopriecka-displeja' => 6.1,
                    'kapacita-baterie' => 3349,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá', 'Červená'],
                ],
            ],
            [
                'name' => 'Apple iPhone 15 256 GB',
                'description' => 'iPhone 15 s 6,1" Super Retina XDR displejom, čipom A16 Bionic, dynamickým ostrovom a 48 MP hlavným fotoaparátom. Interná pamäť 256 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '256 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Full HD',
                    'uhlopriecka-displeja' => 6.1,
                    'kapacita-baterie' => 3349,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá', 'Červená'],
                ],
            ],
            [
                'name' => 'Apple iPhone 15 512 GB',
                'description' => 'iPhone 15 s 6,1" Super Retina XDR displejom, čipom A16 Bionic, dynamickým ostrovom a 48 MP hlavným fotoaparátom. Interná pamäť 512 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '512 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Full HD',
                    'uhlopriecka-displeja' => 6.1,
                    'kapacita-baterie' => 3349,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá', 'Červená'],
                ],
            ],

            // ── Apple iPhone 15 Pro ──────────────────────────────────────────────
            [
                'name' => 'Apple iPhone 15 Pro 128 GB',
                'description' => 'Titanový iPhone 15 Pro s čipom A17 Pro, tlačidlom Action, USB-C 3.0 a 48 MP trojitým fotosystémom. Interná pamäť 128 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '128 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.1,
                    'kapacita-baterie' => 3274,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá'],
                ],
            ],
            [
                'name' => 'Apple iPhone 15 Pro 256 GB',
                'description' => 'Titanový iPhone 15 Pro s čipom A17 Pro, tlačidlom Action, USB-C 3.0 a 48 MP trojitým fotosystémom. Interná pamäť 256 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '256 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.1,
                    'kapacita-baterie' => 3274,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá'],
                ],
            ],
            [
                'name' => 'Apple iPhone 15 Pro 512 GB',
                'description' => 'Titanový iPhone 15 Pro s čipom A17 Pro, tlačidlom Action, USB-C 3.0 a 48 MP trojitým fotosystémom. Interná pamäť 512 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '512 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.1,
                    'kapacita-baterie' => 3274,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá'],
                ],
            ],
            [
                'name' => 'Apple iPhone 15 Pro 1 TB',
                'description' => 'Titanový iPhone 15 Pro s čipom A17 Pro, tlačidlom Action, USB-C 3.0 a 48 MP trojitým fotosystémom. Interná pamäť 1 TB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '1 TB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.1,
                    'kapacita-baterie' => 3274,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá'],
                ],
            ],

            // ── Apple iPhone 15 Pro Max ──────────────────────────────────────────
            [
                'name' => 'Apple iPhone 15 Pro Max 256 GB',
                'description' => 'Najväčší iPhone s 6,7" displejom, tetragonálnym zoom objektívom 5x, čipom A17 Pro a titánovým rámom. Interná pamäť 256 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '256 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.7,
                    'kapacita-baterie' => 4422,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá'],
                ],
            ],
            [
                'name' => 'Apple iPhone 15 Pro Max 512 GB',
                'description' => 'Najväčší iPhone s 6,7" displejom, tetragonálnym zoom objektívom 5x, čipom A17 Pro a titánovým rámom. Interná pamäť 512 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '512 GB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.7,
                    'kapacita-baterie' => 4422,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá'],
                ],
            ],
            [
                'name' => 'Apple iPhone 15 Pro Max 1 TB',
                'description' => 'Najväčší iPhone s 6,7" displejom, tetragonálnym zoom objektívom 5x, čipom A17 Pro a titánovým rámom. Interná pamäť 1 TB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '1 TB',
                    'operacny-system' => 'iOS',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.7,
                    'kapacita-baterie' => 4422,
                    'farba' => ['Čierna', 'Biela', 'Strieborná', 'Modrá'],
                ],
            ],

            // ── Samsung Galaxy S24 ───────────────────────────────────────────────
            [
                'name' => 'Samsung Galaxy S24 128 GB',
                'description' => 'Kompaktná vlajková loď s 6,2" Dynamic AMOLED displejom, čipom Exynos 2400 a funkciami Galaxy AI. Interná pamäť 128 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '128 GB',
                    'operacny-system' => 'Android',
                    'rozlisenie-displeja' => 'Full HD',
                    'uhlopriecka-displeja' => 6.2,
                    'kapacita-baterie' => 4000,
                    'farba' => ['Čierna', 'Strieborná', 'Modrá'],
                ],
            ],
            [
                'name' => 'Samsung Galaxy S24 256 GB',
                'description' => 'Kompaktná vlajková loď s 6,2" Dynamic AMOLED displejom, čipom Exynos 2400 a funkciami Galaxy AI. Interná pamäť 256 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '256 GB',
                    'operacny-system' => 'Android',
                    'rozlisenie-displeja' => 'Full HD',
                    'uhlopriecka-displeja' => 6.2,
                    'kapacita-baterie' => 4000,
                    'farba' => ['Čierna', 'Strieborná', 'Modrá'],
                ],
            ],

            // ── Samsung Galaxy S24 Ultra ─────────────────────────────────────────
            [
                'name' => 'Samsung Galaxy S24 Ultra 256 GB',
                'description' => 'Špičkový Samsung s titanovým rámom, zabudovaným S Pen, 200 MP fotoaparátom a 6,8" QHD+ displejom. Interná pamäť 256 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '256 GB',
                    'operacny-system' => 'Android',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.8,
                    'kapacita-baterie' => 5000,
                    'farba' => ['Čierna', 'Strieborná'],
                ],
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra 512 GB',
                'description' => 'Špičkový Samsung s titanovým rámom, zabudovaným S Pen, 200 MP fotoaparátom a 6,8" QHD+ displejom. Interná pamäť 512 GB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '512 GB',
                    'operacny-system' => 'Android',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.8,
                    'kapacita-baterie' => 5000,
                    'farba' => ['Čierna', 'Strieborná'],
                ],
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra 1 TB',
                'description' => 'Špičkový Samsung s titanovým rámom, zabudovaným S Pen, 200 MP fotoaparátom a 6,8" QHD+ displejom. Interná pamäť 1 TB.',
                'category' => 'smartfony',
                'attrs' => [
                    'kapacita-uloziska' => '1 TB',
                    'operacny-system' => 'Android',
                    'rozlisenie-displeja' => 'Quad HD',
                    'uhlopriecka-displeja' => 6.8,
                    'kapacita-baterie' => 5000,
                    'farba' => ['Čierna', 'Strieborná'],
                ],
            ],

            // ── Apple MacBook Air M3 ─────────────────────────────────────────────
            [
                'name' => 'Apple MacBook Air 13" M3 8 GB / 256 GB',
                'description' => 'Najtenko navrhnutý MacBook Air s 13,6" Liquid Retina displejom, čipom M3, 8 GB RAM a 256 GB SSD.',
                'category' => 'notebooky',
                'attrs' => [
                    'operacny-system' => 'macOS',
                    'operacna-pamat-ram' => '8 GB',
                    'kapacita-uloziska' => '256 GB',
                    'uhlopriecka-displeja' => 13.6,
                ],
            ],
            [
                'name' => 'Apple MacBook Air 13" M3 16 GB / 512 GB',
                'description' => 'Najtenko navrhnutý MacBook Air s 13,6" Liquid Retina displejom, čipom M3, 16 GB RAM a 512 GB SSD.',
                'category' => 'notebooky',
                'attrs' => [
                    'operacny-system' => 'macOS',
                    'operacna-pamat-ram' => '16 GB',
                    'kapacita-uloziska' => '512 GB',
                    'uhlopriecka-displeja' => 13.6,
                ],
            ],
            [
                'name' => 'Apple MacBook Air 13" M3 24 GB / 2 TB',
                'description' => 'Najtenko navrhnutý MacBook Air s 13,6" Liquid Retina displejom, čipom M3, 24 GB RAM a 2 TB SSD.',
                'category' => 'notebooky',
                'attrs' => [
                    'operacny-system' => 'macOS',
                    'kapacita-uloziska' => '2 TB',
                    'uhlopriecka-displeja' => 13.6,
                ],
            ],

            // ── Apple MacBook Pro M4 Pro ─────────────────────────────────────────
            [
                'name' => 'Apple MacBook Pro 14" M4 Pro 24 GB / 512 GB',
                'description' => 'Profesionálny MacBook Pro s 14,2" Liquid Retina XDR displejom, čipom M4 Pro, 24 GB RAM a 512 GB SSD.',
                'category' => 'notebooky',
                'attrs' => [
                    'operacny-system' => 'macOS',
                    'kapacita-uloziska' => '512 GB',
                    'uhlopriecka-displeja' => 14.2,
                ],
            ],
            [
                'name' => 'Apple MacBook Pro 14" M4 Pro 24 GB / 1 TB',
                'description' => 'Profesionálny MacBook Pro s 14,2" Liquid Retina XDR displejom, čipom M4 Pro, 24 GB RAM a 1 TB SSD.',
                'category' => 'notebooky',
                'attrs' => [
                    'operacny-system' => 'macOS',
                    'kapacita-uloziska' => '1 TB',
                    'uhlopriecka-displeja' => 14.2,
                ],
            ],
            [
                'name' => 'Apple MacBook Pro 14" M4 Pro 48 GB / 1 TB',
                'description' => 'Profesionálny MacBook Pro s 14,2" Liquid Retina XDR displejom, čipom M4 Pro, 48 GB RAM a 1 TB SSD.',
                'category' => 'notebooky',
                'attrs' => [
                    'operacny-system' => 'macOS',
                    'kapacita-uloziska' => '1 TB',
                    'uhlopriecka-displeja' => 14.2,
                ],
            ],

            // ── Apple iPad Pro M4 ────────────────────────────────────────────────
            [
                'name' => 'Apple iPad Pro 13" M4 256 GB Wi-Fi',
                'description' => 'Najtenko navrhnutý produkt Apple s 13" Ultra Retina XDR OLED displejom, čipom M4 a 256 GB úložiskom. Verzia Wi-Fi.',
                'category' => 'tablety',
                'attrs' => [
                    'operacny-system' => 'iOS',
                    'kapacita-uloziska' => '256 GB',
                    'uhlopriecka-displeja' => 13.0,
                    'rozlisenie-displeja' => 'Quad HD',
                ],
            ],
            [
                'name' => 'Apple iPad Pro 13" M4 512 GB Wi-Fi',
                'description' => 'Najtenko navrhnutý produkt Apple s 13" Ultra Retina XDR OLED displejom, čipom M4 a 512 GB úložiskom. Verzia Wi-Fi.',
                'category' => 'tablety',
                'attrs' => [
                    'operacny-system' => 'iOS',
                    'kapacita-uloziska' => '512 GB',
                    'uhlopriecka-displeja' => 13.0,
                    'rozlisenie-displeja' => 'Quad HD',
                ],
            ],
            [
                'name' => 'Apple iPad Pro 13" M4 1 TB Wi-Fi',
                'description' => 'Najtenko navrhnutý produkt Apple s 13" Ultra Retina XDR OLED displejom, čipom M4 a 1 TB úložiskom. Verzia Wi-Fi.',
                'category' => 'tablety',
                'attrs' => [
                    'operacny-system' => 'iOS',
                    'kapacita-uloziska' => '1 TB',
                    'uhlopriecka-displeja' => 13.0,
                    'rozlisenie-displeja' => 'Quad HD',
                ],
            ],

            // ── LG OLED C3 ───────────────────────────────────────────────────────
            [
                'name' => 'LG OLED C3 55"',
                'description' => '55" OLED televízor s dokonalou čiernou, procesorom α9 Gen6 AI, 120 Hz panelom a Dolby Vision IQ & Atmos.',
                'category' => 'televizory',
                'attrs' => [
                    'uhlopriecka-displeja' => 55,
                    'rozlisenie-displeja' => '4K Ultra HD',
                    'smart-tv' => 1,
                ],
            ],
            [
                'name' => 'LG OLED C3 65"',
                'description' => '65" OLED televízor s dokonalou čiernou, procesorom α9 Gen6 AI, 120 Hz panelom a Dolby Vision IQ & Atmos.',
                'category' => 'televizory',
                'attrs' => [
                    'uhlopriecka-displeja' => 65,
                    'rozlisenie-displeja' => '4K Ultra HD',
                    'smart-tv' => 1,
                ],
            ],
            [
                'name' => 'LG OLED C3 77"',
                'description' => '77" OLED televízor s dokonalou čiernou, procesorom α9 Gen6 AI, 120 Hz panelom a Dolby Vision IQ & Atmos.',
                'category' => 'televizory',
                'attrs' => [
                    'uhlopriecka-displeja' => 77,
                    'rozlisenie-displeja' => '4K Ultra HD',
                    'smart-tv' => 1,
                ],
            ],
            [
                'name' => 'Samsung Neo QLED QN90C 55"',
                'description' => '55" 4K Neo QLED televízor s technológiou Mini LED, Neural Quantum Procesorom 4K a 144 Hz obnovovacou frekvenciou.',
                'category' => 'televizory',
                'attrs' => [
                    'uhlopriecka-displeja' => 55,
                    'rozlisenie-displeja' => '4K Ultra HD',
                    'smart-tv' => 1,
                ],
            ],

            // ── Audio ────────────────────────────────────────────────────────────
            [
                'name' => 'Apple AirPods Pro 2. generácie',
                'description' => 'Bezdrôtové slúchadlá s aktívnym potlačením hluku, adaptívnym zvukom, priestorovým zvukom a USB-C nabíjacím puzdrom.',
                'category' => 'audio-a-reproduktory',
                'attrs' => [
                    'farba' => ['Biela'],
                ],
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Prémiové over-ear slúchadlá s popredným potlačením hluku, až 30 h výdržou a multipoint Bluetooth pripojením.',
                'category' => 'audio-a-reproduktory',
                'attrs' => [
                    'farba' => ['Čierna', 'Strieborná'],
                ],
            ],
            [
                'name' => 'JBL Charge 5',
                'description' => 'Prenosný Bluetooth reproduktor s IP67 odolnosťou, výkonným basovým radiátorom a funkciou nabíjania iných zariadení.',
                'category' => 'audio-a-reproduktory',
                'attrs' => [
                    'farba' => ['Čierna', 'Modrá', 'Červená'],
                ],
            ],

            // ── Príslušenstvo ────────────────────────────────────────────────────
            [
                'name' => 'Apple Watch Series 10 42mm',
                'description' => 'Najtenko navrhnuté Apple Watch s 42mm displejom, funkciou detekcie spánkovej apnoe, GPS a rýchlym nabíjaním.',
                'category' => 'prislusenstvo',
                'attrs' => [],
            ],
            [
                'name' => 'Apple Watch Series 10 46mm',
                'description' => 'Najtenko navrhnuté Apple Watch s 46mm displejom, funkciou detekcie spánkovej apnoe, GPS a rýchlym nabíjaním.',
                'category' => 'prislusenstvo',
                'attrs' => [],
            ],

            // ── Nabíjačky ────────────────────────────────────────────────────────
            [
                'name' => 'Apple 30W USB-C Power Adapter',
                'description' => 'Kompaktná 30W USB-C nabíjačka Apple s podporou rýchleho nabíjania pre iPhone 15, iPad a MacBook.',
                'category' => 'nabijacky',
                'attrs' => [],
            ],
            [
                'name' => 'Anker 735 GaNPrime 65W',
                'description' => 'Kompaktná 65W GaN nabíjačka s dvoma USB-C a jedným USB-A portom - nabitie MacBooku za 59 minút.',
                'category' => 'nabijacky',
                'attrs' => [],
            ],

            // ── Obaly a kryty ────────────────────────────────────────────────────
            [
                'name' => 'Spigen Tough Armor pre iPhone 15 Pro',
                'description' => 'Dvojvrstvový odolný kryt s technológiou vzduchových vankúšov, integrovaným stojanom a certifikáciou MIL-STD-810G.',
                'category' => 'obaly-a-kryty',
                'attrs' => [
                    'farba' => ['Čierna', 'Strieborná'],
                ],
            ],
            [
                'name' => 'Apple Silicone Case pre iPhone 15',
                'description' => 'Originálny silikónový kryt Apple s mikrovláknovou vnútornou vrstvou a podporou MagSafe nabíjania.',
                'category' => 'obaly-a-kryty',
                'attrs' => [
                    'farba' => ['Čierna', 'Biela', 'Modrá'],
                ],
            ],

            // ── Počítačové príslušenstvo ─────────────────────────────────────────
            [
                'name' => 'Logitech MX Master 3S',
                'description' => 'Ergonomická bezdrôtová myš s tichými tlačidlami, MagSpeed rolovacím kolieskom a 8K DPI senzorom.',
                'category' => 'pocitacove-prislusenstvo',
                'attrs' => [
                    'farba' => ['Čierna', 'Strieborná'],
                ],
            ],
            [
                'name' => 'Keychron K2 Pro Wireless',
                'description' => 'Kompaktná 75% mechanická bezdrôtová klávesnica s RGB podsvietením, QMK/VIA podporou a hot-swap soketmi.',
                'category' => 'pocitacove-prislusenstvo',
                'attrs' => [
                    'farba' => ['Čierna'],
                ],
            ],
        ];

        $imageMap = [
            'Apple iPhone 15 Pro Max' => 'iphone-15-pro-max.jpg',
            'Apple iPhone 15 Pro' => 'iphone-15-pro.jpg',
            'Apple iPhone 15' => 'iphone-15.jpg',
            'Samsung Galaxy S24 Ultra' => 'samsung-s24-ultra.jpg',
            'Samsung Galaxy S24' => 'samsung-s24.jpg',
            'Apple MacBook Pro' => 'macbook-pro-m4.jpg',
            'Apple MacBook Air' => 'macbook-air-m3.jpg',
            'Apple iPad Pro' => 'ipad-pro-m4.jpg',
            'LG OLED C3' => 'lg-oled-c3.jpg',
            'Samsung Neo QLED' => 'samsung-neo-qled.jpg',
            'Apple AirPods' => 'airpods-pro.jpg',
            'Sony WH-1000XM5' => 'sony-wh1000xm5.jpg',
            'JBL Charge 5' => 'jbl-charge-5.jpg',
            'Apple Watch' => 'apple-watch-s10.jpg',
            'Apple 30W' => 'apple-30w-charger.jpg',
            'Anker 735' => 'anker-charger.jpg',
            'Spigen' => 'spigen-case.jpg',
            'Apple Silicone Case' => 'apple-silicone-case.jpg',
            'Logitech MX Master' => 'logitech-mx-master.jpg',
            'Keychron K2' => 'keychron-k2.jpg',
        ];

        foreach ($products as $item) {
            $product = GlobalProduct::create([
                'category_id' => Category::where('slug', $item['category'])->first()?->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'is_active' => true,
            ]);

            $this->attachAttributes($product, $item['attrs'], $attrs);

            $imagePath = null;
            foreach ($imageMap as $prefix => $file) {
                if (str_starts_with($item['name'], $prefix)) {
                    $imagePath = public_path("images/products/{$file}");
                    break;
                }
            }

            try {
                if ($imagePath && file_exists($imagePath)) {
                    $product->addMediaFromString(file_get_contents($imagePath))
                        ->usingFileName(basename($imagePath))
                        ->usingName($product->name)
                        ->toMediaCollection('global_products');
                } else {
                    $product->addMediaFromUrl("https://placehold.co/600x400.jpeg?text={$product->slug}")
                        ->toMediaCollection('global_products');
                }
            } catch (\Exception $e) {
                $this->command->warn("Failed to attach image for: {$product->name}: {$e->getMessage()}");
            }
        }
    }

    /**
     * @param  array<string, string|int|float|list<string>>  $attrData
     * @param  \Illuminate\Support\Collection<string, Attribute>  $attrs
     */
    private function attachAttributes(GlobalProduct $product, array $attrData, \Illuminate\Support\Collection $attrs): void
    {
        foreach ($attrData as $slug => $values) {
            $attribute = $attrs->get($slug);

            if (! $attribute) {
                continue;
            }

            foreach ((array) $values as $value) {
                if ($attribute->type === 'select') {
                    $attrValue = $attribute->attributeValues->firstWhere('value', $value);

                    if (! $attrValue) {
                        continue;
                    }

                    GlobalProductAttribute::create([
                        'global_product_id' => $product->id,
                        'attribute_id' => $attribute->id,
                        'attribute_value_id' => $attrValue->id,
                        'custom_value' => null,
                    ]);
                } else {
                    GlobalProductAttribute::create([
                        'global_product_id' => $product->id,
                        'attribute_id' => $attribute->id,
                        'attribute_value_id' => null,
                        'custom_value' => (string) $value,
                    ]);
                }
            }
        }
    }
}
