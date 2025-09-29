<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan ID dari kategori yang sudah di-seed
        $kategoriMap = DB::table('kategori')->pluck('id', 'kategori')->toArray();

        $subKategoriData = [
            // ATK
            ['kategori' => 'ATK', 'sub_kategori' => 'BABY LOTION', 'keterangan' => 'Perlengkapan bayi seperti minyak, bedak, dan lotion.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'BABY MAKAN', 'keterangan' => 'Makanan bubur instan dan sereal bayi.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'BABY POPOK', 'keterangan' => 'Popok bayi sekali pakai berbagai ukuran.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'BABY SUSU', 'keterangan' => 'Susu formula dan pertumbuhan untuk bayi dan balita.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'ELEKTRO', 'keterangan' => 'Barang elektronik seperti baterai, kabel, dan lampu.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'KANTOR', 'keterangan' => 'Alat Tulis Kantor (ATK) seperti pulpen, buku, dan stapler.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'KOSMETIK', 'keterangan' => 'Produk perawatan diri, rambut, wajah, dan deodorant.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'MAINAN', 'keterangan' => 'Aneka mainan, lilin, dan alat permainan anak.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'OBAT CAIR', 'keterangan' => 'Obat-obatan dalam bentuk cair atau sirup (Komix, Antangin).'],
            ['kategori' => 'ATK', 'sub_kategori' => 'OBAT NYAMUK', 'keterangan' => 'Obat nyamuk bakar, elektrik, dan semprot.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'OBAT OLES', 'keterangan' => 'Obat luar, balsem, koyo, dan lotion anti nyamuk.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'OBAT TABLET', 'keterangan' => 'Obat-obatan dalam bentuk pil atau tablet (Bodrex, Paracetamol).'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PART BAN', 'keterangan' => 'Suku cadang motor berupa ban luar dan ban dalam.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PART BEARING', 'keterangan' => 'Suku cadang motor berupa bearing/laher.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PART BUSHING', 'keterangan' => 'Suku cadang motor berupa busi.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PART LAIN', 'keterangan' => 'Suku cadang motor dan perlengkapan lain (Lem Besi, Tali Gas).'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PART OLI', 'keterangan' => 'Oli mesin dan oli gardan untuk motor.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PERLENGKAPAN', 'keterangan' => 'Perlengkapan umum seperti kaos kaki, sandal, dan sikat.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PLASTIK', 'keterangan' => 'Berbagai jenis kantong plastik dan plastik gula.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'SOFTEX', 'keterangan' => 'Pembalut wanita.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'TISU', 'keterangan' => 'Tisu kering dan tisu basah.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'PECAH BELAH', 'keterangan' => 'Peralatan rumah tangga seperti panci, sapu, gayung, dan piring.'],
            ['kategori' => 'ATK', 'sub_kategori' => 'SUB', 'keterangan' => 'Sub kategori yang tidak terdefinisi (Diabaikan).'],

            // MAKANAN
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'BISKUIT', 'keterangan' => 'Aneka biskuit, wafer, dan crackers kemasan besar maupun sachet.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'COKELAT BOX', 'keterangan' => 'Cokelat kemasan kotak/besar.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'COKELAT CUP', 'keterangan' => 'Cokelat kemasan cup/kecil.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'JAJANAN', 'keterangan' => 'Jajanan tradisional atau snack kemasan kecil.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'JELLY', 'keterangan' => 'Jelly atau agar-agar instan.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'PERMEN', 'keterangan' => 'Berbagai jenis permen.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'ROTI', 'keterangan' => 'Roti, bolu, atau kue kemasan.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'SNACK 1000', 'keterangan' => 'Snack dengan harga jual eceran sekitar Rp 1000.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'SNACK 2000', 'keterangan' => 'Snack dengan harga jual eceran sekitar Rp 2000.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'SNACK 500', 'keterangan' => 'Snack dengan harga jual eceran sekitar Rp 500.'],
            ['kategori' => 'MAKANAN', 'sub_kategori' => 'SNACK BESAR', 'keterangan' => 'Snack dengan kemasan besar.'],

            // MINUMAN
            ['kategori' => 'MINUMAN', 'sub_kategori' => 'BOTOL/ KALENG', 'keterangan' => 'Minuman kemasan botol atau kaleng.'],
            ['kategori' => 'MINUMAN', 'sub_kategori' => 'CUP', 'keterangan' => 'Minuman kemasan cup kecil.'],
            ['kategori' => 'MINUMAN', 'sub_kategori' => 'GALON', 'keterangan' => 'Air minum kemasan galon.'],
            ['kategori' => 'MINUMAN', 'sub_kategori' => 'KOTAK', 'keterangan' => 'Minuman kemasan kotak (Tetra Pak).'],
            ['kategori' => 'MINUMAN', 'sub_kategori' => 'SACHET', 'keterangan' => 'Minuman bubuk sachet yang diseduh.'],

            // PROJECT
            ['kategori' => 'PROJECT', 'sub_kategori' => 'CICI_RIRIS SNACK', 'keterangan' => 'Barang khusus untuk Proyek Cici Riris (Snack).'],
            ['kategori' => 'PROJECT', 'sub_kategori' => 'DEDDY BUMBU', 'keterangan' => 'Barang khusus untuk Proyek Deddy (Bumbu).'],
            ['kategori' => 'PROJECT', 'sub_kategori' => 'DEDDY SNACK', 'keterangan' => 'Barang khusus untuk Proyek Deddy (Snack).'],
            ['kategori' => 'PROJECT', 'sub_kategori' => 'MAULIDA SNACK', 'keterangan' => 'Barang khusus untuk Proyek Maulida (Snack).'],
            ['kategori' => 'PROJECT', 'sub_kategori' => 'PAMAN KERUPUK', 'keterangan' => 'Barang khusus untuk Proyek Paman (Kerupuk).'],
            ['kategori' => 'PROJECT', 'sub_kategori' => 'CICI_RIRIS BAJU', 'keterangan' => 'Barang khusus untuk Proyek Cici Riris (Pakaian).'],
            ['kategori' => 'PROJECT', 'sub_kategori' => 'CICI_RIRIS ELEKTRO', 'keterangan' => 'Barang khusus untuk Proyek Cici Riris (Elektronik).'],

            // PULSA
            ['kategori' => 'PULSA', 'sub_kategori' => 'BIASA', 'keterangan' => 'Pulsa reguler untuk berbagai operator.'],
            ['kategori' => 'PULSA', 'sub_kategori' => 'DANA', 'keterangan' => 'Top-up saldo E-Wallet (DANA, OVO, dll).'],
            ['kategori' => 'PULSA', 'sub_kategori' => 'PAKET', 'keterangan' => 'Paket data internet.'],
            ['kategori' => 'PULSA', 'sub_kategori' => 'PLN', 'keterangan' => 'Token Listrik PLN.'],

            // ROKOK
            ['kategori' => 'ROKOK', 'sub_kategori' => 'KOREK', 'keterangan' => 'Korek api dan pemantik.'],
            ['kategori' => 'ROKOK', 'sub_kategori' => 'MAHAL', 'keterangan' => 'Rokok dengan harga di atas rata-rata.'],
            ['kategori' => 'ROKOK', 'sub_kategori' => 'MURAH', 'keterangan' => 'Rokok dengan harga ekonomis.'],
            ['kategori' => 'ROKOK', 'sub_kategori' => 'ROKOK LAIN', 'keterangan' => 'Rokok merek atau jenis lain yang tidak terklasifikasi.'],
            ['kategori' => 'ROKOK', 'sub_kategori' => 'SEDANG', 'keterangan' => 'Rokok dengan harga menengah.'],

            // SEMBAKO
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'BAKO2', 'keterangan' => 'Bahan Makanan lain-lain (Telur, Gula, dll).'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'BUMBU DAPUR', 'keterangan' => 'Bumbu-bumbu dapur seperti garam, penyedap rasa, dan bumbu instan.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'KECAP/ SAOS', 'keterangan' => 'Kecap dan Saos aneka merek dan ukuran.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'KERUPUK', 'keterangan' => 'Kerupuk mentah dan kerupuk kemasan besar.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'MIE CUP', 'keterangan' => 'Mie instan dalam kemasan cup.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'MIE DOS', 'keterangan' => 'Mie instan dalam kemasan dus (karton).'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'MIE DUO', 'keterangan' => 'Mie instan dalam kemasan pak/bungkus isi 2.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'MIE KECIL', 'keterangan' => 'Mie instan dalam kemasan bungkus (satuan).'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'MIE LAIN', 'keterangan' => 'Jenis mie instan atau mie kering lain.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'PEWANGI', 'keterangan' => 'Pewangi dan pelicin pakaian.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'SABUN', 'keterangan' => 'Sabun cuci, sabun mandi, dan deterjen.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'SEMBAKO', 'keterangan' => 'Kebutuhan Sembako utama lainnya (Minyak, Beras, dll).'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'SHAMPO BTL', 'keterangan' => 'Shampo dalam kemasan botol.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'SHAMPO RTG', 'keterangan' => 'Shampo dalam kemasan rentengan/sachet.'],
            ['kategori' => 'SEMBAKO', 'sub_kategori' => 'TEPUNG', 'keterangan' => 'Berbagai jenis tepung untuk memasak.'],

        ];

        $insertData = [];
        foreach ($subKategoriData as $data) {
            // Pastikan Kategori ada sebelum mencoba mengambil ID-nya
            $kategoriId = $kategoriMap[$data['kategori']] ?? null;

            if ($kategoriId) {
                $insertData[] = [
                    'id_kategori' => $kategoriId,
                    'sub_kategori' => $data['sub_kategori'],
                    'keterangan' => $data['keterangan'],
                ];
            }
        }

        DB::table('sub_kategori')->insert($insertData);
    }
}
