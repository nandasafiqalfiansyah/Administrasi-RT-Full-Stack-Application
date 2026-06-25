<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Resident;
use App\Models\House;
use App\Models\ResidentHouseHistory;
use App\Models\PaymentType;
use App\Models\MonthlyBill;
use App\Models\Payment;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'name' => 'Admin RT',
            'email' => 'admin@rtjagoan.test',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Ketua RT',
            'email' => 'ketua@rtjagoan.test',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'role' => 'ketua_rt',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Bendahara',
            'email' => 'bendahara@rtjagoan.test',
            'password' => Hash::make('password'),
            'phone' => '081234567892',
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        // Create Payment Types
        $satpam = PaymentType::create([
            'nama' => 'Iuran Satpam',
            'slug' => 'iuran-satpam',
            'nominal' => 100000,
            'deskripsi' => 'Iuran keamanan satpam bulanan',
        ]);

        $kebersihan = PaymentType::create([
            'nama' => 'Iuran Kebersihan',
            'slug' => 'iuran-kebersihan',
            'nominal' => 15000,
            'deskripsi' => 'Iuran kebersihan lingkungan bulanan',
        ]);

        // Create Expense Categories
        $categories = [
            ['nama' => 'Gaji Satpam', 'slug' => 'gaji-satpam', 'deskripsi' => 'Gaji satpam bulanan'],
            ['nama' => 'Listrik', 'slug' => 'listrik', 'deskripsi' => 'Biaya listrik fasilitas umum'],
            ['nama' => 'Perbaikan', 'slug' => 'perbaikan', 'deskripsi' => 'Biaya perbaikan infrastruktur'],
            ['nama' => 'Kebersihan', 'slug' => 'kebersihan', 'deskripsi' => 'Biaya kebersihan lingkungan'],
            ['nama' => 'Administrasi', 'slug' => 'administrasi', 'deskripsi' => 'Biaya administrasi'],
            ['nama' => 'Lainnya', 'slug' => 'lainnya', 'deskripsi' => 'Pengeluaran lainnya'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::create($cat);
        }

        // Create 15 residents (tetap)
        $namaTetap = [
            'Ahmad Fauzi', 'Siti Nurhaliza', 'Bambang Supriyanto', 'Dewi Sartika',
            'Hendra Gunawan', 'Rina Marlina', 'Agus Wijaya', 'Fitri Handayani',
            'Dedi Kurniawan', 'Mega Wati', 'Rudi Hartono', 'Nina Sari',
            'Eko Prasetyo', 'Lina Marlina', 'Yudi Setiawan',
        ];

        $residents = [];
        foreach ($namaTetap as $i => $nama) {
            $residents[] = Resident::create([
                'nik' => '3201' . str_pad((string)($i + 1), 12, '0', STR_PAD_LEFT),
                'nama_lengkap' => $nama,
                'status' => 'tetap',
                'nomor_hp' => '0812' . str_pad((string)($i + 100000), 6, '0', STR_PAD_LEFT),
                'status_menikah' => $i < 10 ? 'kawin' : ($i < 13 ? 'belum_kawin' : 'cerai_hidup'),
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => now()->subYears(rand(25, 60))->subDays(rand(1, 365)),
                'agama' => ['Islam', 'Islam', 'Islam', 'Kristen', 'Katolik'][rand(0, 4)],
                'pekerjaan' => ['PNS', 'Swasta', 'Wirausaha', 'Guru', 'Dokter', 'IRT'][rand(0, 5)],
                'tanggal_masuk' => now()->subYears(rand(1, 5))->subDays(rand(1, 365)),
                'catatan' => null,
                'is_active' => true,
            ]);
        }

        // Create 5 additional kontrak residents
        $kontrakResidents = [];
        $namaKontrak = ['Fajar Pratama', 'Putri Ayu', 'Dimas Ardiansyah', 'Wulan Sari', 'Adi Nugroho'];
        foreach ($namaKontrak as $i => $nama) {
            $kontrakResidents[] = Resident::create([
                'nik' => '3202' . str_pad((string)($i + 1), 12, '0', STR_PAD_LEFT),
                'nama_lengkap' => $nama,
                'status' => 'kontrak',
                'nomor_hp' => '0813' . str_pad((string)($i + 100000), 6, '0', STR_PAD_LEFT),
                'status_menikah' => 'belum_kawin',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => now()->subYears(rand(20, 35))->subDays(rand(1, 365)),
                'agama' => 'Islam',
                'pekerjaan' => ['Karyawan', 'Mahasiswa', 'Freelancer'][rand(0, 2)],
                'tanggal_masuk' => now()->subMonths(rand(3, 11)),
                'catatan' => null,
                'is_active' => true,
            ]);
        }

        // Create 20 houses
        $blokList = ['A', 'B', 'C', 'D'];
        $houses = [];
        $nomor = 1;
        foreach ($blokList as $blok) {
            for ($j = 1; $j <= 5; $j++) {
                $houses[] = House::create([
                    'nomor_rumah' => "{$blok}{$j}",
                    'blok' => $blok,
                    'status' => ($nomor <= 15) ? 'dihuni' : 'tidak_dihuni',
                    'current_resident_id' => ($nomor <= 15) ? $residents[$nomor - 1]->id : null,
                    'catatan' => $nomor > 15 ? 'Rumah kosong' : null,
                ]);
                $nomor++;
            }
        }

        // Create resident house histories
        foreach ($houses as $i => $house) {
            if ($house->status === 'dihuni') {
                ResidentHouseHistory::create([
                    'house_id' => $house->id,
                    'resident_id' => $house->current_resident_id,
                    'tanggal_masuk' => Resident::find($house->current_resident_id)->tanggal_masuk,
                    'tanggal_keluar' => null,
                    'status' => 'tetap',
                ]);

                // Add some past history for context
                if ($i % 3 === 0) {
                    ResidentHouseHistory::create([
                        'house_id' => $house->id,
                        'resident_id' => $residents[($i + 5) % 15]->id,
                        'tanggal_masuk' => now()->subYears(2),
                        'tanggal_keluar' => now()->subYear()->subMonth(),
                        'status' => 'tetap',
                    ]);
                }
            }
        }

        // Assign kontrak residents to empty houses (garage 16-20)
        foreach ($kontrakResidents as $i => $resident) {
            $houseIndex = 15 + $i; // House index 15-19
            if ($houseIndex < count($houses)) {
                $house = $houses[$houseIndex];
                $house->update([
                    'status' => 'dihuni',
                    'current_resident_id' => $resident->id,
                ]);

                ResidentHouseHistory::create([
                    'house_id' => $house->id,
                    'resident_id' => $resident->id,
                    'tanggal_masuk' => $resident->tanggal_masuk,
                    'tanggal_keluar' => $resident->tanggal_masuk->copy()->addYear(),
                    'status' => 'kontrak',
                ]);
            }
        }

        // Generate bills and payments for the past 12 months
        $occupiedHouses = House::dihuni()->get();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        for ($bulan = 1; $bulan <= $currentMonth; $bulan++) {
            $tahun = $currentYear;

            foreach ($occupiedHouses as $house) {
                foreach ([$satpam, $kebersihan] as $paymentType) {
                    $isPaid = rand(0, 10) > 2; // 80% chance paid

                    $bill = MonthlyBill::create([
                        'house_id' => $house->id,
                        'payment_type_id' => $paymentType->id,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'nominal' => $paymentType->nominal,
                        'status' => $isPaid ? 'lunas' : 'belum_lunas',
                        'jatuh_tempo' => now()->day(10)->month($bulan)->year($tahun),
                        'tanggal_lunas' => $isPaid ? now()->day(rand(1, 10))->month($bulan)->year($tahun) : null,
                    ]);

                    if ($isPaid) {
                        Payment::create([
                            'kode_pembayaran' => Payment::generateKode(),
                            'house_id' => $house->id,
                            'resident_id' => $house->current_resident_id,
                            'payment_type_id' => $paymentType->id,
                            'monthly_bill_id' => $bill->id,
                            'nominal' => $paymentType->nominal,
                            'tanggal_bayar' => now()->day(rand(1, 10))->month($bulan)->year($tahun),
                            'metode_pembayaran' => ['tunai', 'transfer'][rand(0, 1)],
                            'created_by' => $admin->id,
                        ]);
                    }
                }
            }
        }

        // Create expenses for the past 12 months
        $expenseNames = [
            'Gaji Satpam', 'Listrik Pos RT', 'Perbaikan Jalan', 'Perbaikan Selokan',
            'Pembelian Alat Kebersihan', 'Biaya Administrasi', 'Kegiatan Gotong Royong',
            'Pengecetan Pagar', 'Pembelian Lampu Jalan', 'Perbaikan Atap Pos RT',
        ];

        $expenseCategoryIds = ExpenseCategory::pluck('id')->toArray();

        for ($bulan = 1; $bulan <= $currentMonth; $bulan++) {
            // 2-4 expenses per month
            $numExpenses = rand(2, 4);
            for ($e = 0; $e < $numExpenses; $e++) {
                $categoryId = $expenseCategoryIds[array_rand($expenseCategoryIds)];
                $nama = $expenseNames[array_rand($expenseNames)];
                $nominal = $categoryId === 1 ? 500000 : rand(50000, 300000);

                Expense::create([
                    'expense_category_id' => $categoryId,
                    'nama_pengeluaran' => $nama,
                    'nominal' => $nominal,
                    'tanggal' => now()->day(rand(1, 28))->month($bulan)->year($currentYear),
                    'keterangan' => 'Pengeluaran bulan ' . $bulan,
                    'created_by' => $admin->id,
                ]);
            }
        }

        // Activity log
        ActivityLog::log('seed', 'system', 'Database seeded with dummy data', null, null, null, $admin->id);
    }
}