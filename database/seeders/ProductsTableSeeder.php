<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Picture;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('products')->truncate();
        Schema::enableForeignKeyConstraints();

        Product::insert([
            'name' => 'Mobile Legends',
            'code' => 'ML',
            'product_category_id' => 1,
            'description' => 'Mobile Legends: Bang Bang adalah sebuah permainan piranti bergerak berjenis arena pertarungan daring multipemain yang dikembangkan dan diterbitkan oleh Moonton, Yang kini menjadi anak perusahaan dari ByteDance.',
            'company' => 'Moonton',
            'how_to_order' => '<p><b>Cara Melihat Player ID / Server ID Mobile Legends</b></p><p><img src="https://mitragamers.com/assets/img/game/ml.jpg" class="w-100"></p><p>Untuk mengetahui informasi player ID dan server ID Mobile Legends, silahkan masuk ke menu "profile", lalu lihat bagian kanan atas.</p>',
            'input_format' => '{}',
            'slug' => 'mobile-legends',
            'markup_reseller_silver' => 1,
            'markup_reseller_gold' => 1,
            'markup_reseller_vip' => 1,
            'markup_user' => 2,
            'default_picture' => '',
            'default_cover' => '',
            'ordering' => 0,
            'status' => 'active',
            'provider' => 'lapakgaming',
            'provider_code' => 'ML',
            'provider_country' => 'id',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Product::insert([
            // TODO: adjust seeder for other products
            [
                'name' => 'Free Fire',
                /*'category' => 'game',*/
                'company' => 'Garena',
                'description' => 'Garena Free Fire atau biasa disebut Free Fire adalah permainan battle royale yang dikembangkan oleh 111 Dots Studio dan diterbitkan oleh Garena untuk Android dan iOS. Itu menjadi permainan seluler yang paling banyak diunduh secara global pada tahun 2019.',
                'how_to_order' => '<p><b>Cara Melihat Player ID Free Fire</b></p><p><img src="https://mitragamers.com/assets/img/game/ff.jpg" class="w-100"></p><p>Untuk menemukan informasi Player ID Free Fire dengan membuka menu "profile akun". Player ID terletak di bawah foto dan name akun.</p>',
                'slug' => 'free-fire',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'FF',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'PUBG Mobile',
                /*'category' => 'game',*/
                'company' => 'Tencent Games',
                'description' => 'PUBG Mobile adalah video game battle royale gratis yang dikembangkan oleh LightSpeed ​​& Quantum Studio, sebuah divisi dari Tencent Games. Yang merupakan game mobile yang diadaptasi dari PUBG: Battlegrounds dan dirilis untuk Android dan iOS pada 19 Maret 2018.',
                'how_to_order' => '<p><b>Cara Melihat Player ID Pubg Mobile</b></p><p><img src="https://mitragamers.com/assets/img/game/pubg.jpg" class="w-100"></p><p>Untuk menemukan informasi Player ID PUBG Mobile dengan membuka "profil" Di sana akan terlihat Player ID akun kamu</p>',
                'slug' => 'pubg-mobile',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'UCPUBGM',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Genshin Impact',
                /*'category' => 'game',*/
                'company' => 'Mihoyo',
                'description' => 'Genshin Impact adalah role-playing game yang dikembangkan oleh perusahaan Cina miHoYo, dan pertama kali diterbitkan pada tahun 2020. Game ini menampilkan lingkungan dunia terbuka bergaya anime dan sistem pertarungan berbasis aksi menggunakan sihir unsur dan pergantian karakter.',
                'how_to_order' => '<p><b>Cara Melihat UID Genshin Impact</b></p><p><img src="https://mitragamers.com/assets/img/game/genshin.jpg" class="w-100"></p><p>Untuk menemukan informasi UID Genshin Impact dengan melihat informasi UID di bagian pojok kanan bawah layar permainan</p>',
                'slug' => 'genshin-impact',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'GI',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Valorant',
                /*'category' => 'game',*/
                'company' => 'Riot',
                'description' => 'Valorant adalah game first-person hero shooter gratis yang dikembangkan dan diterbitkan oleh Riot Games, untuk Microsoft Windows. Pertama kali dirilis dengan name kode Project A pada Oktober 2019, game ini memulai periode beta tertutup dengan akses terbatas pada 7 April 2020, diikuti dengan rilis resmi pada 2 Juni 2020.',
                'how_to_order' => '<p><b>Cara Riot ID Valorant</b></p><p>Pemain pertama-tama harus masuk ke akun Riot di riotgames.com. Setelah masuk, kamu dapat mengklik “RIOT ID” di sisi kiri kotak akun.</p>',
                'slug' => 'valorant',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'VAL',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'LoL: Wild Rift',
                /*'category' => 'game',*/
                'company' => 'Riot',
                'description' => 'League of Legends: Wild Rift adalah multiplayer online battle arena mobile game yang dikembangkan dan diterbitkan oleh Riot Games untuk Android dan iOS. Game gratis untuk dimainkan ini adalah versi modifikasi dari game PC League of Legends',
                'how_to_order' => '<p><b>Cara Melihat Riot ID LoL - Wild Rift</b></p><p><img src="https://mitragamers.com/assets/img/game/lol.jpg" class="w-100"></p><p>Untuk menemukan informasi Riot ID LoL - Wild Rift dengan membuka menu "profile akun". Riot ID terletak di bawah foto dan name akun.</p>',
                'slug' => 'lol-wild-rift',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'LOL',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Call of Duty Mobile',
                /*'category' => 'game',*/
                'company' => 'Garena',
                'description' => 'Call of Duty: Mobile adalah game free-to-play shooter game yang dikembangkan oleh TiMi Studio Group dan diterbitkan oleh Activision untuk Android dan iOS. Dirilis pada 1 Oktober 2019',
                'how_to_order' => '<p><b>Cara Melihat Open ID COD Mobile</b></p><p>Pemain pertama-tama harus membuka menu Setting, lalu klik Lainnya dan Open ID ada di layar bagian bawah</p>',
                'slug' => 'call-of-duty-mobile',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'CODM',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Arena of Valer',
                /*'category' => 'game',*/
                'company' => 'Garena',
                'description' => 'Arena of Valor, sebelumnya Strike of Kings, adalah adaptasi internasional dari Honor of Kings, multiplayer online battle arena yang dikembangkan oleh TiMi Studio Group dan diterbitkan oleh Level Infinite untuk Android, iOS, dan Nintendo Switch untuk pasar di luar Daratan Tiongkok',
                'how_to_order' => '<p><b>Cara Melihat Open ID AOV</b></p><p>Buka pengaturan game yang seperti gear di sebelah kanan atas. Pilih pada menu "Umum" lalu scroll ke bawah untuk menemukan "OpenID" yang berada di sebelah "NamePemain".</p>',
                'slug' => 'arena-of-valor',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'AOV',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sausage Man',
                /*'category' => 'game',*/
                'company' => 'XD Global',
                'description' => 'Sausage Man adalah game bergenre battle royale. Dikembangkan oleh XD Entertainment, Sausage Man baru dirilis tanggal 29 Juni 2021 untuk dimainkan di perangkat berbasis Android.',
                'how_to_order' => '<p><b>Cara Melihat User ID Sausage Man</b></p><p><img src="https://mitragamers.com/assets/img/game/sausage.jpg" class="w-100"></p><p>Masuk ke lobby Sausage Man, kemudian tap icon “Baris Tiga”. ID akun Sausage Man berada di sebelah kanan tombol “Ganti Akun” atau yang saya tandai “Arrow”.</p>',
                'slug' => 'sausage-man',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'SMN',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Garena Shell',
                /*'category' => 'voucher',*/
                'company' => 'Garena',
                'description' => 'Garena Shell adalah mata uang Garena untuk top up game Garena',
                'how_to_order' => '<p>Masuk ke situs kiosgamer.co.id dan pilih shell top up lalu isi kode Garena Shell yang sudah diberikan</p>',
                'slug' => 'garena-shell',
                'status' => 'active',
                'product_category_id' => 1,
                'provider' => 'lapakgaming',
                'provider_code' => 'VGS',
                'provider_country' => 'id',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        Picture::insert([
            [
                'pictureable_id' => 1,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'mobile-legends.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 2,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'ff.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 3,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'pubg.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 4,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'genshin.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 5,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'valorant.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 6,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'lol.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 7,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'codm.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 8,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'aov.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 9,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'sausage.jpeg',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pictureable_id' => 10,
                'pictureable_type' => 'App\Models\Product',
                'path' => 'img/product',
                'file_name' => 'garena-shells.png',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
