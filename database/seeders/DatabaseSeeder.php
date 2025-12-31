<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kavling;
use App\Models\Peralatan;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Gallery;
use App\Models\Announcement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ========================
        // 1. USERS
        // ========================

        // Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@luhurcamp.com'],
            [
                'name' => 'Admin LuhurCamp',
                'email' => 'admin@luhurcamp.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );

        // Customer Users
        $customers = [];
        $customerData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'phone' => '081234567891'],
            ['name' => 'Siti Rahayu', 'email' => 'siti@gmail.com', 'phone' => '081234567892'],
            ['name' => 'Ahmad Wijaya', 'email' => 'ahmad@gmail.com', 'phone' => '081234567893'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@gmail.com', 'phone' => '081234567894'],
            ['name' => 'Rudi Hermawan', 'email' => 'rudi@gmail.com', 'phone' => '081234567895'],
        ];

        foreach ($customerData as $data) {
            $customers[] = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                ])
            );
        }

        $this->command->info('✅ Users created: 1 admin + ' . count($customers) . ' customers');

        // ========================
        // 2. KAVLINGS
        // ========================

        $kavlings = [
            ['nama' => 'Kavling A1', 'slug' => 'kavling-a1', 'kapasitas' => 4, 'harga_per_malam' => 150000, 'status' => 'aktif', 'deskripsi' => 'Area dekat sumber air dengan pemandangan sunrise yang menakjubkan. Cocok untuk keluarga kecil.'],
            ['nama' => 'Kavling A2', 'slug' => 'kavling-a2', 'kapasitas' => 6, 'harga_per_malam' => 200000, 'status' => 'aktif', 'deskripsi' => 'Area luas untuk kelompok besar dengan akses mudah ke toilet dan mushola.'],
            ['nama' => 'Kavling B1', 'slug' => 'kavling-b1', 'kapasitas' => 4, 'harga_per_malam' => 150000, 'status' => 'aktif', 'deskripsi' => 'Area teduh dengan pohon pinus yang rindang, suasana sejuk sepanjang hari.'],
            ['nama' => 'Kavling B2', 'slug' => 'kavling-b2', 'kapasitas' => 8, 'harga_per_malam' => 300000, 'status' => 'aktif', 'deskripsi' => 'Area premium dengan fasilitas lengkap, view 360° pegunungan.'],
            ['nama' => 'Kavling C1', 'slug' => 'kavling-c1', 'kapasitas' => 4, 'harga_per_malam' => 175000, 'status' => 'aktif', 'deskripsi' => 'Area dengan view sunset terbaik, dekat dengan area api unggun.'],
            ['nama' => 'Kavling C2', 'slug' => 'kavling-c2', 'kapasitas' => 5, 'harga_per_malam' => 180000, 'status' => 'maintenance', 'deskripsi' => 'Area strategis di tengah camping ground, akses ke semua fasilitas.'],
        ];

        $kavlingModels = [];
        foreach ($kavlings as $kavling) {
            $kavlingModels[] = Kavling::updateOrCreate(['slug' => $kavling['slug']], $kavling);
        }

        $this->command->info('✅ ' . count($kavlings) . ' Kavlings created');

        // ========================
        // 3. PERALATAN
        // ========================

        $peralatan = [
            ['nama' => 'Tenda Dome 4P', 'kategori' => 'tenda', 'stok_total' => 15, 'harga_sewa' => 75000, 'kondisi' => 'baik', 'deskripsi' => 'Tenda kapasitas 4 orang, waterproof, mudah dipasang'],
            ['nama' => 'Tenda Dome 2P', 'kategori' => 'tenda', 'stok_total' => 10, 'harga_sewa' => 50000, 'kondisi' => 'baik', 'deskripsi' => 'Tenda kapasitas 2 orang, ringan dan compact'],
            ['nama' => 'Tenda Camping 6P', 'kategori' => 'tenda', 'stok_total' => 5, 'harga_sewa' => 120000, 'kondisi' => 'baik', 'deskripsi' => 'Tenda besar untuk keluarga, 2 ruangan'],
            ['nama' => 'Kompor Portable', 'kategori' => 'masak', 'stok_total' => 10, 'harga_sewa' => 25000, 'kondisi' => 'baik', 'deskripsi' => 'Kompor gas portable dengan regulator, aman digunakan'],
            ['nama' => 'Nesting Set', 'kategori' => 'masak', 'stok_total' => 8, 'harga_sewa' => 20000, 'kondisi' => 'baik', 'deskripsi' => 'Set alat masak camping 4-5 pcs, anti lengket'],
            ['nama' => 'Sleeping Bag', 'kategori' => 'tidur', 'stok_total' => 20, 'harga_sewa' => 30000, 'kondisi' => 'baik', 'deskripsi' => 'Sleeping bag nyaman untuk suhu 10-20°C'],
            ['nama' => 'Matras', 'kategori' => 'tidur', 'stok_total' => 20, 'harga_sewa' => 15000, 'kondisi' => 'baik', 'deskripsi' => 'Matras foam anti lembab, tebal 5cm'],
            ['nama' => 'Lampu Tenda', 'kategori' => 'lainnya', 'stok_total' => 15, 'harga_sewa' => 10000, 'kondisi' => 'baik', 'deskripsi' => 'Lampu LED rechargeable, terang hingga 12 jam'],
            ['nama' => 'Kursi Lipat', 'kategori' => 'lainnya', 'stok_total' => 12, 'harga_sewa' => 15000, 'kondisi' => 'perlu_perbaikan', 'deskripsi' => 'Kursi camping lipat portable, beban max 100kg'],
            ['nama' => 'Meja Lipat', 'kategori' => 'lainnya', 'stok_total' => 8, 'harga_sewa' => 20000, 'kondisi' => 'baik', 'deskripsi' => 'Meja camping lipat aluminium, ringan dan kuat'],
        ];

        $peralatanModels = [];
        foreach ($peralatan as $item) {
            $peralatanModels[] = Peralatan::updateOrCreate(['nama' => $item['nama']], $item);
        }

        $this->command->info('✅ ' . count($peralatan) . ' Equipment items created');

        // ========================
        // 4. BOOKINGS
        // ========================

        $bookings = [];
        $statuses = ['pending', 'confirmed', 'completed', 'rejected', 'cancelled'];

        // Prevent duplicate bookings on re-seeding
        if (Booking::count() > 0) {
            $this->command->info('⏩ Bookings already exist, skipping booking generation.');
        } else {
            // Past bookings (completed)
            for ($i = 0; $i < 10; $i++) {
                $customer = $customers[array_rand($customers)];
                $kavling = $kavlingModels[array_rand($kavlingModels)];
                $checkIn = now()->subDays(rand(30, 90));
                $checkOut = $checkIn->copy()->addDays(rand(1, 3));
                $nights = $checkIn->diffInDays($checkOut);

                $booking = Booking::create([
                    'code' => 'BK-' . now()->format('ymd') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'user_id' => $customer->id,
                    'kavling_id' => $kavling->id,
                    'tanggal_check_in' => $checkIn,
                    'tanggal_check_out' => $checkOut,
                    'total_harga' => $kavling->harga_per_malam * $nights,
                    'status' => 'completed',
                ]);
                $bookings[] = $booking;
            }

            // Recent & upcoming bookings (various statuses)
            for ($i = 0; $i < 15; $i++) {
                $customer = $customers[array_rand($customers)];
                $kavling = $kavlingModels[array_rand($kavlingModels)];
                $checkIn = now()->addDays(rand(-5, 30));
                $checkOut = $checkIn->copy()->addDays(rand(1, 4));
                $nights = $checkIn->diffInDays($checkOut);
                $status = $statuses[array_rand($statuses)];

                // Calculate total with equipment
                $equipmentTotal = 0;
                $booking = Booking::create([
                    'code' => 'BK-' . now()->format('ymd') . '-' . str_pad($i + 11, 3, '0', STR_PAD_LEFT),
                    'user_id' => $customer->id,
                    'kavling_id' => $kavling->id,
                    'tanggal_check_in' => $checkIn,
                    'tanggal_check_out' => $checkOut,
                    'total_harga' => $kavling->harga_per_malam * $nights,
                    'status' => $status,
                    'bukti_pembayaran' => $status !== 'pending' ? 'bukti/sample.jpg' : null,
                    'rejection_reason' => $status === 'rejected' ? 'Bukti pembayaran tidak valid' : null,
                    'qr_code' => $status === 'confirmed' ? 'qrcodes/' . Str::random(10) . '.svg' : null,
                ]);

                // Add random equipment to some bookings
                if (rand(0, 1)) {
                    $equipItems = array_rand($peralatanModels, rand(1, 3));
                    if (!is_array($equipItems))
                        $equipItems = [$equipItems];

                    foreach ($equipItems as $equipIdx) {
                        $equip = $peralatanModels[$equipIdx];
                        $qty = rand(1, 2);
                        $subtotal = $equip->harga_sewa * $qty * $nights;

                        BookingItem::create([
                            'booking_id' => $booking->id,
                            'peralatan_id' => $equip->id,
                            'jumlah' => $qty,
                            'harga_sewa' => $equip->harga_sewa,
                            'subtotal' => $subtotal,
                        ]);

                        $equipmentTotal += $subtotal;
                    }

                    // Update total harga
                    $booking->update(['total_harga' => $booking->total_harga + $equipmentTotal]);
                }

                $bookings[] = $booking;
            }

            $this->command->info('✅ ' . count($bookings) . ' Bookings created');
        }

        // ========================
        // 5. GALLERIES
        // ========================

        $captions = [
            'Sunrise cantik dari Kavling B1 🌅',
            'Malam berbintang di LuhurCamp ✨',
            'Camping bareng keluarga tercinta 🏕️',
            'View Gunung Luhur yang menakjubkan 🏔️',
            'BBQ night dengan teman-teman 🔥',
            'Morning coffee with a view ☕',
            'Fog rolling in at dawn 🌫️',
            'Kids having fun at the campsite 👨‍👩‍👧‍👦',
            'Hammock life 🌴',
            'Night photography session 📸',
        ];

        $galleryStatuses = ['approved', 'approved', 'approved', 'pending', 'rejected'];

        for ($i = 0; $i < 20; $i++) {
            Gallery::create([
                'user_id' => $customers[array_rand($customers)]->id,
                'image_path' => 'galleries/sample-' . ($i + 1) . '.jpg',
                'caption' => $captions[array_rand($captions)],
                'status' => $galleryStatuses[array_rand($galleryStatuses)],
            ]);
        }

        $this->command->info('✅ 20 Gallery photos created');

        // ========================
        // 6. ANNOUNCEMENTS
        // ========================

        $announcements = [
            [
                'title' => 'Selamat Datang di LuhurCamp! 🏕️',
                'content' => 'Nikmati pengalaman camping terbaik di ketinggian 1.500 mdpl dengan fasilitas lengkap dan pemandangan spektakuler.',
                'type' => 'info',
                'is_active' => true,
            ],
            [
                'title' => 'Promo Tahun Baru 2025 🎉',
                'content' => 'Dapatkan diskon 20% untuk semua pemesanan kavling selama periode 1-7 Januari 2025. Gunakan kode: NEWYEAR25',
                'type' => 'success',
                'is_active' => true,
            ],
            [
                'title' => 'Peringatan Cuaca ⚠️',
                'content' => 'Diperkirakan hujan lebat pada malam hari. Pastikan tenda Anda tertutup rapat dan barang-barang berharga diamankan.',
                'type' => 'warning',
                'is_active' => true,
            ],
            [
                'title' => 'Fasilitas Baru: Toilet Premium',
                'content' => 'Kami telah menambahkan 5 unit toilet premium dengan air hangat di area Blok B. Gratis untuk semua pengunjung!',
                'type' => 'info',
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::updateOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }

        $this->command->info('✅ ' . count($announcements) . ' Announcements created');

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('📧 Admin Login: admin@luhurcamp.com / password123');
    }
}
