<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('barang')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // =================================================================
        // AMBIL ID DARI SUB KATEGORI YANG SUDAH ADA
        // PENTING: Ganti 'NAMA SUB KATEGORI' dengan nama sub kategori yang benar di database Anda.
        // Jika nama sub kategori Anda tidak ada dalam DB, Anda perlu menambahkannya dulu di SubKategoriSeeder.
        // =================================================================
        $subKategoriBabyLotionId = DB::table('sub_kategori')->where('sub_kategori', 'BABY LOTION')->first()->id ?? 1; // Contoh default ID 1
        $subKategoriBabyMakanId = DB::table('sub_kategori')->where('sub_kategori', 'BABY MAKAN')->first()->id ?? 2;
        $subKategoriBabyPopokId = DB::table('sub_kategori')->where('sub_kategori', 'BABY POPOK')->first()->id ?? 3;
        $subKategoriBabySusuId = DB::table('sub_kategori')->where('sub_kategori', 'BABY SUSU')->first()->id ?? 4;
        $subKategoriElektroId = DB::table('sub_kategori')->where('sub_kategori', 'ELEKTRO')->first()->id ?? 5;
        $subKategoriKantorId = DB::table('sub_kategori')->where('sub_kategori', 'KANTOR')->first()->id ?? 6;
        $subKategoriKosmetikId = DB::table('sub_kategori')->where('sub_kategori', 'KOSMETIK')->first()->id ?? 7;
        $subKategoriMainanId = DB::table('sub_kategori')->where('sub_kategori', 'MAINAN')->first()->id ?? 8;
        $subKategoriObatCairId = DB::table('sub_kategori')->where('sub_kategori', 'OBAT CAIR')->first()->id ?? 9;
        $subKategoriObatNyamukId = DB::table('sub_kategori')->where('sub_kategori', 'OBAT NYAMUK')->first()->id ?? 10;
        $subKategoriObatOlesId = DB::table('sub_kategori')->where('sub_kategori', 'OBAT OLES')->first()->id ?? 11;
        $subKategoriObatTabletId = DB::table('sub_kategori')->where('sub_kategori', 'OBAT TABLET')->first()->id ?? 12;
        $subKategoriPartBanId = DB::table('sub_kategori')->where('sub_kategori', 'PART BAN')->first()->id ?? 13;
        $subKategoriPartBearingId = DB::table('sub_kategori')->where('sub_kategori', 'PART BEARING')->first()->id ?? 14;
        $subKategoriPartBushingId = DB::table('sub_kategori')->where('sub_kategori', 'PART BUSHING')->first()->id ?? 15;
        $subKategoriPartLainId = DB::table('sub_kategori')->where('sub_kategori', 'PART LAIN')->first()->id ?? 16;
        $subKategoriPartOliId = DB::table('sub_kategori')->where('sub_kategori', 'PART OLI')->first()->id ?? 17;
        $subKategoriPerlengkapanId = DB::table('sub_kategori')->where('sub_kategori', 'PERLENGKAPAN')->first()->id ?? 18;
        $subKategoriPlastikId = DB::table('sub_kategori')->where('sub_kategori', 'PLASTIK')->first()->id ?? 19;
        $subKategoriSoftexId = DB::table('sub_kategori')->where('sub_kategori', 'SOFTEX')->first()->id ?? 20;
        $subKategoriTisuId = DB::table('sub_kategori')->where('sub_kategori', 'TISU')->first()->id ?? 21;
        $subKategoriPecahBelahId = DB::table('sub_kategori')->where('sub_kategori', 'PECAH BELAH')->first()->id ?? 22;
        $subKategoriBiskuitId = DB::table('sub_kategori')->where('sub_kategori', 'BISKUIT')->first()->id ?? 23;
        $subKategoriCokelatBoxId = DB::table('sub_kategori')->where('sub_kategori', 'COKELAT BOX')->first()->id ?? 24;
        $subKategoriCokelatCupId = DB::table('sub_kategori')->where('sub_kategori', 'COKELAT CUP')->first()->id ?? 25;
        $subKategoriJajananId = DB::table('sub_kategori')->where('sub_kategori', 'JAJANAN')->first()->id ?? 26;
        $subKategoriJellyId = DB::table('sub_kategori')->where('sub_kategori', 'JELLY')->first()->id ?? 27;
        $subKategoriPermenId = DB::table('sub_kategori')->where('sub_kategori', 'PERMEN')->first()->id ?? 28;
        $subKategoriRotiId = DB::table('sub_kategori')->where('sub_kategori', 'ROTI')->first()->id ?? 29;
        $subKategoriSnack1000Id = DB::table('sub_kategori')->where('sub_kategori', 'SNACK 1000')->first()->id ?? 30;
        $subKategoriSnack2000Id = DB::table('sub_kategori')->where('sub_kategori', 'SNACK 2000')->first()->id ?? 31;
        $subKategoriSnack500Id = DB::table('sub_kategori')->where('sub_kategori', 'SNACK 500')->first()->id ?? 32;


        DB::table('barang')->insert([
            // BABY LOTION
            [
                'id_sub_kategori' => $subKategoriBabyLotionId,
                'kode_barang' => 'MINYAK PUTIH-B',
                'nama_barang' => 'MINYAK PUTIH (BESAR)',
                'keterangan' => '22K @60ml',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyLotionId,
                'kode_barang' => 'MINYAK PUTIH-K',
                'nama_barang' => 'MINYAK PUTIH (KECIL)',
                'keterangan' => '7K @15ml',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyLotionId,
                'kode_barang' => 'MINYAK PUTIH-S',
                'nama_barang' => 'MINYAK PUTIH (SEDANG)',
                'keterangan' => '13K @30ml',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyLotionId,
                'kode_barang' => 'MINYAK TELON-K',
                'nama_barang' => 'MINYAK TELON (KECIL)',
                'keterangan' => '12K @30ml',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyLotionId,
                'kode_barang' => 'MINYAK TELON-S',
                'nama_barang' => 'MINYAK TELON (SEDANG)',
                'keterangan' => '16K @60ml',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyLotionId,
                'kode_barang' => 'PUPUR BABY-K',
                'nama_barang' => 'PUPUR MY BABY (KECIL)',
                'keterangan' => '4K @50gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyLotionId,
                'kode_barang' => 'PUPUR BABY-S',
                'nama_barang' => 'PUPUR MY BABY (SEDANG)',
                'keterangan' => '7K @113gr',
            ],

            // BABY MAKAN
            [
                'id_sub_kategori' => $subKategoriBabyMakanId,
                'kode_barang' => 'SUN AYAM',
                'nama_barang' => 'SUN (AYAM KAMPUNG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBabyMakanId,
                'kode_barang' => 'SUN BERAS MERAH',
                'nama_barang' => 'SUN (BERAS MERAH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBabyMakanId,
                'kode_barang' => 'SUN KACANG IJO',
                'nama_barang' => 'SUN (KACANG IJO)',
                'keterangan' => null,
            ],

            // BABY POPOK
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MAMY POKO-L',
                'nama_barang' => 'MAMY POKO (L)',
                'keterangan' => '28',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-L',
                'nama_barang' => 'POPOK MARRIES (L)',
                'keterangan' => '30',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-M',
                'nama_barang' => 'POPOK MARRIES (M)',
                'keterangan' => '34',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-XL',
                'nama_barang' => 'POPOK MARRIES (XL)',
                'keterangan' => '26',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-XXL 28',
                'nama_barang' => 'POPOK MARRIES (XXL)',
                'keterangan' => '28',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-XXL 18',
                'nama_barang' => 'POPOK MARRIES (XXL)',
                'keterangan' => '18',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-L 44',
                'nama_barang' => 'POPOK MARRIES (L)',
                'keterangan' => '44',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-XL 38',
                'nama_barang' => 'POPOK MARRIES (XL)',
                'keterangan' => '38',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-L PCS',
                'nama_barang' => 'POPOK MARRIES (L)',
                'keterangan' => '1 pcs',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-M PCS',
                'nama_barang' => 'POPOK MARRIES (M)',
                'keterangan' => '1 pcs',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-XL PCS',
                'nama_barang' => 'POPOK MARRIES (XL)',
                'keterangan' => '1 pcs',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-S',
                'nama_barang' => 'POPOK MARRIES (S)',
                'keterangan' => '40',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-L 28',
                'nama_barang' => 'POPOK MARRIES (L)',
                'keterangan' => '28',
            ],
            [
                'id_sub_kategori' => $subKategoriBabyPopokId,
                'kode_barang' => 'MARRIES-M 32',
                'nama_barang' => 'POPOK MARRIES (M)',
                'keterangan' => '32',
            ],

            // BABY SUSU
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'DANCOW 1+',
                'nama_barang' => 'SUSU DANCOW 1+',
                'keterangan' => '1-3 TAHUN',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM 0-6',
                'nama_barang' => 'SUSU SGM 0-6 TAHUN',
                'keterangan' => '400gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM 5+',
                'nama_barang' => 'SUSU SGM 5+ TAHUN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM 6-12',
                'nama_barang' => 'SUSU SGM 6-12 TAHUN',
                'keterangan' => '400gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM SOYA 1+',
                'nama_barang' => 'SUSU SGM SOYA 1+',
                'keterangan' => '700gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU VIDORAN 1-3',
                'nama_barang' => 'SUSU VIDORAN SMART 1-3 TH',
                'keterangan' => '925gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM 1+ 600',
                'nama_barang' => 'SUSU SGM 1+ TAHUN',
                'keterangan' => '600 gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM 5+ 900',
                'nama_barang' => 'SUSU SGM 5+ TAHUN',
                'keterangan' => '900 gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM 1+ 900',
                'nama_barang' => 'SUSU SGM 1+ TAHUN',
                'keterangan' => '900 gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU SGM 1-3+ 600',
                'nama_barang' => 'SUSU SGM (MADU) 1-3 TAHUN',
                'keterangan' => '600 gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBabySusuId,
                'kode_barang' => 'SUSU MORINAGA',
                'nama_barang' => 'SUSU MORINAGA CHILSCHOOL 3-12 TAHUN',
                'keterangan' => '800gr',
            ],

            // ELEKTRO
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'BATERAI ABC-B',
                'nama_barang' => 'BATERAI ABC (BESAR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'BATERAI ABC-K',
                'nama_barang' => 'BATERAI ABC (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'BATERAI ABC-S',
                'nama_barang' => 'BATERAI ABC (SEDANG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'LAMPU LAKU 10W',
                'nama_barang' => 'LAMPU LAKU-LAKU',
                'keterangan' => '@10W',
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'LAMPU SMD 10W',
                'nama_barang' => 'LAMPU SMD',
                'keterangan' => '@10W',
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'MY LED 10W',
                'nama_barang' => 'MY LED T10',
                'keterangan' => '@10W',
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'KABEL-10M',
                'nama_barang' => 'KABEL',
                'keterangan' => '10 M',
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'LAMPU LUNA 10W',
                'nama_barang' => 'LAMPU LUNA LED',
                'keterangan' => '@10W',
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'LAMPU SMD 20W',
                'nama_barang' => 'LAMPU SMD',
                'keterangan' => '@20W',
            ],
            [
                'id_sub_kategori' => $subKategoriElektroId,
                'kode_barang' => 'KOMPOR RINNAI (RI-302S)',
                'nama_barang' => 'KOMPOR GAS RINNAI (RI-302S)',
                'keterangan' => null,
            ],

            // KANTOR
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'AMPLOP',
                'nama_barang' => 'AMPLOP GUMMED (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'AMPLOP-B',
                'nama_barang' => 'AMPLOP PAPERLINE (BESAR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'AMPLOP-K',
                'nama_barang' => 'AMPLOP PAPERLINE (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'PEN-PILOT',
                'nama_barang' => 'BALLPOINT (PILOT)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'PEN-STD',
                'nama_barang' => 'BALLPOINT (STANDART)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'BUKU FOLIO',
                'nama_barang' => 'BUKU FOLIO',
                'keterangan' => '@20K',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'DODO 38',
                'nama_barang' => 'BUKU TULIS DODO',
                'keterangan' => 'ISI @38',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'DODO 58',
                'nama_barang' => 'BUKU TULIS DODO',
                'keterangan' => 'ISI @58',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'SIDU 38',
                'nama_barang' => 'BUKU TULIS SIDU',
                'keterangan' => 'ISI @38',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'SIDU 58',
                'nama_barang' => 'BUKU TULIS SIDU',
                'keterangan' => 'ISI @58',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'GUNTING-B',
                'nama_barang' => 'GUNTING (BESAR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'KWITANSI-K',
                'nama_barang' => 'KWITANSI (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'LEM KOREA',
                'nama_barang' => 'LEM KOREA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'PAKU PICIK-B',
                'nama_barang' => 'PAKU PICIK (BINTANG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'PENGGARIS-K',
                'nama_barang' => 'PENGGARIS (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'PENGGEREK',
                'nama_barang' => 'PENGGEREK',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'HOME-B',
                'nama_barang' => 'PENGHAPUS BESAR',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'PENSIL',
                'nama_barang' => 'PENSIL',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'SILET TATRA',
                'nama_barang' => 'SILET TATRA',
                'keterangan' => '(PCS)',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'STAPLER TYPE-10',
                'nama_barang' => 'STAPLER TYPE-10',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'LABEL HARGA',
                'nama_barang' => 'LABEL HARGA',
                'keterangan' => '(TEMBAK)',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'STAPLER-K',
                'nama_barang' => 'STAPLER TYPE-10',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'SILET GOAL',
                'nama_barang' => 'SILET GOAL',
                'keterangan' => '(PCS)',
            ],
            [
                'id_sub_kategori' => $subKategoriKantorId,
                'kode_barang' => 'CASTOL-T',
                'nama_barang' => 'LEM CASTOL',
                'keterangan' => '(TANGGUNG)',
            ],

            // KOSMETIK
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'BIORE MENS-B',
                'nama_barang' => 'BIORE MENS (BESAR)',
                'keterangan' => '32K @100gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'CREAM PONDS',
                'nama_barang' => 'CREAM PONDS',
                'keterangan' => '24K @20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'CUKUR-J',
                'nama_barang' => 'CUKUR JENGGOT KW',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'GARNIER MENS-B',
                'nama_barang' => 'GARNIER MENS (BESAR)',
                'keterangan' => '30K @100ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'GATSBY THC',
                'nama_barang' => 'GATSBY THC',
                'keterangan' => '8K @70gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'GATSBY WAX-B',
                'nama_barang' => 'GATSBY WAX (BESAR)',
                'keterangan' => '19K @75gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'GATSBY WAX-K',
                'nama_barang' => 'GATSBY WAX (KECIL)',
                'keterangan' => '11K @25gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'GATSBY WG-S',
                'nama_barang' => 'GATSBY WG (SEDANG)',
                'keterangan' => '9K @75gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'KATEMBET BABY',
                'nama_barang' => 'KATEMBET BABY',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'KATEMBET CUSSON',
                'nama_barang' => 'KATEMBET CUSSON',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'NIVEA-MAN',
                'nama_barang' => 'NIVEA-MAN DEODORAN',
                'keterangan' => '13K @25ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'NIVEA-WOMAN',
                'nama_barang' => 'NIVEA-WOMAN DEODORAN',
                'keterangan' => '13K @25ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'CASABLANKA-B',
                'nama_barang' => 'PARFUM CASABLANKA (BESAR)',
                'keterangan' => '33K @200ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'PARFUM FRESH',
                'nama_barang' => 'PARFUM FRESH',
                'keterangan' => '12K @100ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'POSH-MAN',
                'nama_barang' => 'PARFUM POSH (MAN)',
                'keterangan' => '20K @150ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'POSH-WOMAN',
                'nama_barang' => 'PARFUM POSH (WOMAN)',
                'keterangan' => '20K @150ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'POMADE-B',
                'nama_barang' => 'POMADE (BESAR)',
                'keterangan' => '30K @80gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'REXONA MAN-SCT',
                'nama_barang' => 'REXONA MAN (SACHET)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'REXONA-ROLL',
                'nama_barang' => 'REXONA ROLL',
                'keterangan' => '@45ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'REXONA WOMAN-SCT',
                'nama_barang' => 'REXONA WOMAN (SACHET)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'VASELINE PINK',
                'nama_barang' => 'VASELINE PINK (WOMAN)',
                'keterangan' => '15K @100ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'CUKUR GOAL',
                'nama_barang' => 'CUKUR JENGGOT GOAL (ORIGINAL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'TANCO-B',
                'nama_barang' => 'MINYAK RAMBUT TANCO',
                'keterangan' => '@10K',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'PONDS AC',
                'nama_barang' => 'PONDS ACTIVATED CHARCOAL',
                'keterangan' => '@100gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'VASELINE PINK-B',
                'nama_barang' => 'VASELINE PINK (WOMAN)',
                'keterangan' => '27K @200ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'BLACK ROCK',
                'nama_barang' => 'BLACK ROCK DEODORAN',
                'keterangan' => '17K @50ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'NIVEA-MAN B',
                'nama_barang' => 'NIVEA-MAN DEODORAN',
                'keterangan' => '20K @50ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'PARFUM ARUNIKA',
                'nama_barang' => 'PARFUM ARUNIKA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'HAND BODY MARINA',
                'nama_barang' => 'HAND BODY MARINA (WOMAN)',
                'keterangan' => '@190ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'HAND BODY SHINZUI',
                'nama_barang' => 'HAND BODY SHINZUI (WOMAN)',
                'keterangan' => '@210ml',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'PONDS FACIAL',
                'nama_barang' => 'PONDS FACIAL FOAM',
                'keterangan' => '@50gr',
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'POSH MAN-SCT',
                'nama_barang' => 'POSH MAN (SACHET)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'KATEMBET-SLT',
                'nama_barang' => 'KATEMBET SELECTION',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'ESKULIN GEL',
                'nama_barang' => 'ESKULIN GEL',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriKosmetikId,
                'kode_barang' => 'HAIR LOTION',
                'nama_barang' => 'HAIR LOTION CUSSON',
                'keterangan' => null,
            ],

            // MAINAN
            [
                'id_sub_kategori' => $subKategoriMainanId,
                'kode_barang' => 'BALON',
                'nama_barang' => 'BALON',
                'keterangan' => 'ISI @50',
            ],
            [
                'id_sub_kategori' => $subKategoriMainanId,
                'kode_barang' => 'JITAK',
                'nama_barang' => 'DOMINO (JITAK)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriMainanId,
                'kode_barang' => 'ORIGAMI',
                'nama_barang' => 'KERTAS ORIGAMI (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriMainanId,
                'kode_barang' => 'LILIN MAINAN-B',
                'nama_barang' => 'LILIN MAINAN (BESAR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriMainanId,
                'kode_barang' => 'LILIN MAINAN-K',
                'nama_barang' => 'LILIN MAINAN (KECIL)',
                'keterangan' => '@PACK',
            ],
            [
                'id_sub_kategori' => $subKategoriMainanId,
                'kode_barang' => 'LILIN MAINAN-S',
                'nama_barang' => 'LILIN MAINAN (SEDANG)',
                'keterangan' => null,
            ],

            // OBAT CAIR
            [
                'id_sub_kategori' => $subKategoriObatCairId,
                'kode_barang' => 'ANTANGIN',
                'nama_barang' => 'ANTANGIN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatCairId,
                'kode_barang' => 'KOMIX-JAHE',
                'nama_barang' => 'KOMIX (JAHE)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatCairId,
                'kode_barang' => 'KOMIX-JERUK NIPIS',
                'nama_barang' => 'KOMIX (JERUK NIPIS)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatCairId,
                'kode_barang' => 'KOMIX-OBH',
                'nama_barang' => 'KOMIX (OBH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatCairId,
                'kode_barang' => 'KOMIX-MINT',
                'nama_barang' => 'KOMIX (PAPPERMINT)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatCairId,
                'kode_barang' => 'TOLAK ANGIN',
                'nama_barang' => 'TOLAK ANGIN',
                'keterangan' => null,
            ],

            // OBAT NYAMUK
            [
                'id_sub_kategori' => $subKategoriObatNyamukId,
                'kode_barang' => 'VAVE-B',
                'nama_barang' => 'VAVE (JUMBO)',
                'keterangan' => 'BAKAR',
            ],
            [
                'id_sub_kategori' => $subKategoriObatNyamukId,
                'kode_barang' => 'VAVE-EL 45',
                'nama_barang' => 'VAVE ELEKTRIK',
                'keterangan' => '45 MALAM',
            ],
            [
                'id_sub_kategori' => $subKategoriObatNyamukId,
                'kode_barang' => 'VAVE-EL 90',
                'nama_barang' => 'VAVE ELEKTRIK',
                'keterangan' => '90 MALAM',
            ],
            [
                'id_sub_kategori' => $subKategoriObatNyamukId,
                'kode_barang' => 'VAVE SEMPROT-S',
                'nama_barang' => 'VAVE SEMPROT',
                'keterangan' => '400 ML',
            ],

            // OBAT OLES
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'FLOSA BIRU',
                'nama_barang' => 'FLOSA BIRU',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'FLOSA MERAH',
                'nama_barang' => 'FLOSA MERAH',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'HANSAPLAS',
                'nama_barang' => 'HANSAPLAS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'GPU',
                'nama_barang' => 'MINYAK URUT GPU',
                'keterangan' => '@30ml',
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'SALONPAS-CABE',
                'nama_barang' => 'SALONPAS (CABE)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'SALONPAS',
                'nama_barang' => 'SALONPAS (ORI)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'SOFFEL',
                'nama_barang' => 'SOFFEL',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'KOMPRES',
                'nama_barang' => 'KOMPRES ANAK (KOOLFEVER)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'KOYO CABE',
                'nama_barang' => 'KOYO CABE (CHILI BRAND)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'AUTAN',
                'nama_barang' => 'AUTAN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatOlesId,
                'kode_barang' => 'KAPUR AJAIB',
                'nama_barang' => 'KAPUR AJAIB',
                'keterangan' => null,
            ],

            // OBAT TABLET
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'AMOKSILIN',
                'nama_barang' => 'AMOKSILIN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'AMPISILIN',
                'nama_barang' => 'AMPISILIN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'BODREX',
                'nama_barang' => 'BODREX',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'BODREX-X',
                'nama_barang' => 'BODREX EXTRA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'BODREX-MR',
                'nama_barang' => 'BODREX MIGRAN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'BODREXIN',
                'nama_barang' => 'BODREXIN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'DECOLSIN',
                'nama_barang' => 'DECOLSIN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'KONIDIN',
                'nama_barang' => 'KONIDIN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'MIXAGRIB',
                'nama_barang' => 'MIXAGRIB',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'MIXAGRIB-FB',
                'nama_barang' => 'MIXAGRIB FLU & BATUK',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'NEO-R',
                'nama_barang' => 'NEO REMACYL',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'OSKADON',
                'nama_barang' => 'OSKADON',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'PARACETAMOL',
                'nama_barang' => 'PARACETAMOL',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'PARAMEX',
                'nama_barang' => 'PARAMEX',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'PONSTAN',
                'nama_barang' => 'PONSTAN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'PROMAG',
                'nama_barang' => 'PROMAG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'SUPER TETRA',
                'nama_barang' => 'SUPER TETRA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'TAWON SAKTI',
                'nama_barang' => 'TAWON SAKTI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'DIATAB',
                'nama_barang' => 'DIATAB',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'ENTROSTOP',
                'nama_barang' => 'ENTROSTOP',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriObatTabletId,
                'kode_barang' => 'OBSAGI',
                'nama_barang' => 'OBSAGI',
                'keterangan' => null,
            ],

            // PART BAN
            [
                'id_sub_kategori' => $subKategoriPartBanId,
                'kode_barang' => 'BAN ANGKONG',
                'nama_barang' => 'BAN ANGKONG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartBanId,
                'kode_barang' => 'BAN DALAM BEBEK',
                'nama_barang' => 'BAN DALAM 275/17 (BEBEK)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartBanId,
                'kode_barang' => 'BAN DALAM MATIC14',
                'nama_barang' => 'BAN DALAM MATIC',
                'keterangan' => '@14',
            ],

            // PART BEARING
            [
                'id_sub_kategori' => $subKategoriPartBearingId,
                'kode_barang' => 'BEARING 6004',
                'nama_barang' => 'BEARING 6004',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartBearingId,
                'kode_barang' => 'BEARING 6201',
                'nama_barang' => 'BEARING 6201',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartBearingId,
                'kode_barang' => 'BEARING 6300',
                'nama_barang' => 'BEARING 6300',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartBearingId,
                'kode_barang' => 'BEARING 6301',
                'nama_barang' => 'BEARING 6301',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartBearingId,
                'kode_barang' => 'BEARING 6202',
                'nama_barang' => 'BEARING 6202',
                'keterangan' => null,
            ],

            // PART BUSHING
            [
                'id_sub_kategori' => $subKategoriPartBushingId,
                'kode_barang' => 'BUSI PANJANG',
                'nama_barang' => 'BUSI PANJANG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartBushingId,
                'kode_barang' => 'BUSI PENDEK',
                'nama_barang' => 'BUSI PENDEK',
                'keterangan' => null,
            ],

            // PART LAIN
            [
                'id_sub_kategori' => $subKategoriPartLainId,
                'kode_barang' => 'LEM BESI',
                'nama_barang' => 'LEM BESI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartLainId,
                'kode_barang' => 'TALI GAS MOTOR',
                'nama_barang' => 'TALI GAS MOTOR',
                'keterangan' => null,
            ],

            // PART OLI
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI MPX 1',
                'nama_barang' => 'OLI MPX 1 (BEBEK)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI MPX 2',
                'nama_barang' => 'OLI MPX 2 (MATIC)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI YAMALUBE',
                'nama_barang' => 'OLI YAMALUBE (MOTOR BEBEK)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI GARDAN-YML',
                'nama_barang' => 'OLI YAMALUBE GARDAN (MOTOR MATIC)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI OYAMA-MT',
                'nama_barang' => 'OLI OYAMA AT (MATIC)',
                'keterangan' => '@800ML',
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI OYAMA-GARDAN',
                'nama_barang' => 'OLI OYAMA MGO K (GARDAN )',
                'keterangan' => '@120ML',
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI OYAMA-4T',
                'nama_barang' => 'OLI OYAMA 4T (BEBEK)',
                'keterangan' => '@800ML',
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI ECSTAR',
                'nama_barang' => 'OLI ECSTAR (SUZUKI)',
                'keterangan' => '@800ML',
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI GARDAN-AHM',
                'nama_barang' => 'OLI GARDAN AHM',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPartOliId,
                'kode_barang' => 'OLI ULTRATEK-4T',
                'nama_barang' => 'OLI ULTRATEK (BEBEK)',
                'keterangan' => null,
            ],

            // PERLENGKAPAN
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'BENANG TENUN',
                'nama_barang' => 'BENANG TENUN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'KAOS KAKI',
                'nama_barang' => 'KAOS KAKI (MURAH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'KAOS KAKI-TNT',
                'nama_barang' => 'KAOS KAKI PANJANG (TENTARA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'KAOS TANGAN-B',
                'nama_barang' => 'KAOS TANGAN (BINTIK)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'KAOS TANGAN-P',
                'nama_barang' => 'KAOS TANGAN (POLOS)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'SANDAL NIPON',
                'nama_barang' => 'SANDAL NIPON',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'SANDAL REMATIK',
                'nama_barang' => 'SANDAL REMATIK',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'SEPATU KARET',
                'nama_barang' => 'SEPATU KARET',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'LILIN',
                'nama_barang' => 'LILIN (PUTIH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'SIKAT BAJU',
                'nama_barang' => 'SIKAT BAJU',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPerlengkapanId,
                'kode_barang' => 'TAS KERJA',
                'nama_barang' => 'TAS GENDONG KERJA (TALI)',
                'keterangan' => null,
            ],

            // PLASTIK
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-B MERAH',
                'nama_barang' => 'PLASTIK BESAR (MERAH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-B UNGU',
                'nama_barang' => 'PLASTIK BESAR (UNGU)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK GULA 1/2 KG',
                'nama_barang' => 'PLASTIK GULA 1/2 KG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-K HITAM',
                'nama_barang' => 'PLASTIK KECIL (HITAM)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-K PUTIH',
                'nama_barang' => 'PLASTIK KECIL (PUTIH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-S HITAM',
                'nama_barang' => 'PLASTIK SEDANG (HITAM)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-S PUTIH',
                'nama_barang' => 'PLASTIK SEDANG (PUTIH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-K BB HITAM',
                'nama_barang' => 'PLASTIK KECIL BAMBU (HITAM)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-K PNC PUTIH',
                'nama_barang' => 'PLASTIK KECIL PANCA BUDI (PUTIH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-S BB HITAM',
                'nama_barang' => 'PLASTIK SEDANG BAMBU (HITAM)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-S PNC PUTIH',
                'nama_barang' => 'PLASTIK SEDANG PANCA BUDI (PUTIH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPlastikId,
                'kode_barang' => 'PLASTIK-S BB PUTIH',
                'nama_barang' => 'PLASTIK SEDANG BAMBU (PUTIH)',
                'keterangan' => null,
            ],

            // SOFTEX
            [
                'id_sub_kategori' => $subKategoriSoftexId,
                'kode_barang' => 'SOFTEX LAURIER XTRA 22',
                'nama_barang' => 'SOFTEX SAYAP 5K LAURIER ACTIVE DAY',
                'keterangan' => '22cm',
            ],
            [
                'id_sub_kategori' => $subKategoriSoftexId,
                'kode_barang' => 'SOFTEX CHARM NIGHT 35',
                'nama_barang' => 'SOFTEX SAYAP 6K CHARM SAFE NIGHT',
                'keterangan' => '35cm',
            ],
            [
                'id_sub_kategori' => $subKategoriSoftexId,
                'kode_barang' => 'SOFTEX CHARM 23',
                'nama_barang' => 'SOFTEX SAYAP 7K CHARM EXTRA MAXI',
                'keterangan' => '23cm',
            ],
            [
                'id_sub_kategori' => $subKategoriSoftexId,
                'kode_barang' => 'SOFTEX SIRIH 23',
                'nama_barang' => 'SOFTEX SAYAP DAUN SIRIH',
                'keterangan' => '23cm',
            ],
            [
                'id_sub_kategori' => $subKategoriSoftexId,
                'kode_barang' => 'SOFTEX LAURIER NIGHT 35',
                'nama_barang' => 'SOFTEX SAYAP 9K LAURIER RELAX NIGHT',
                'keterangan' => '35cm',
            ],
            [
                'id_sub_kategori' => $subKategoriSoftexId,
                'kode_barang' => 'SOFTEX LAURIER XTRA MAXI 25',
                'nama_barang' => 'SOFTEX SAYAP 6K LAURIER ACTIVE DAY X-TRA MAXI',
                'keterangan' => '25cm',
            ],
            [
                'id_sub_kategori' => $subKategoriSoftexId,
                'kode_barang' => 'SOFTEX CHARM 13K',
                'nama_barang' => 'SOFTEX SAYAP 13K CHARM EXTRA MAXI',
                'keterangan' => null,
            ],

            // TISU
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU BASAH-CUSSON',
                'nama_barang' => 'TISU BASAH (CUSSON)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU BASAH-MITU',
                'nama_barang' => 'TISU BASAH (MITU)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU JOLLY',
                'nama_barang' => 'TISU JOLLY (MERAH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU MONTIS',
                'nama_barang' => 'TISU MONTIS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU NICE-K',
                'nama_barang' => 'TISU NICE (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU PASEO',
                'nama_barang' => 'TISU PASEO',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU SERA',
                'nama_barang' => 'TISU SERA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU SOFT',
                'nama_barang' => 'TISU SOFT',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU U',
                'nama_barang' => 'TISU U',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU BASAH-PASEO',
                'nama_barang' => 'TISU BASAH (PASEO)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU TESSA',
                'nama_barang' => 'TISU TESSA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU JOLLY-K',
                'nama_barang' => 'TISU JOLLY KECIL (MERAH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU MULTI',
                'nama_barang' => 'TISU MULTI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriTisuId,
                'kode_barang' => 'TISU BASAH-PASEO MINI',
                'nama_barang' => 'TISU BASAH MINI (PASEO)',
                'keterangan' => null,
            ],

            // PECAH BELAH
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'SAPU V',
                'nama_barang' => 'SAPU VINODA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'SAPU N',
                'nama_barang' => 'SAPU NAGOYA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'PANCI-K',
                'nama_barang' => 'PANCI 16 TELINGA (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'GAYUNG',
                'nama_barang' => 'GAYUNG MANDI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'SERBET',
                'nama_barang' => 'SERBET ROKET',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'KASTOK JD',
                'nama_barang' => 'KASTOK JD (MURAH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'PANCI-B',
                'nama_barang' => 'PANCI 28 TELINGA (BESAR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'MAGIC COM MIYAKO',
                'nama_barang' => 'MAGIC COM (MIYAKO)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'PIRING SET',
                'nama_barang' => 'PIRING SET',
                'keterangan' => '(SDK2,PRG3,MKK1,GLS2)',
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'WAJAN ST',
                'nama_barang' => 'WAJAN STENLIS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'PANCI-S',
                'nama_barang' => 'PANCI TELINGA (SEDANG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'COBEK-TNH',
                'nama_barang' => 'COBEK TANAH',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'ULEK BATU',
                'nama_barang' => 'ULEK BATU',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'PANCI-40',
                'nama_barang' => 'PANCI 40',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'PISAU-APP',
                'nama_barang' => 'PISAU APPLE',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'KAPIT BAJU',
                'nama_barang' => 'KAPIT BAJU (CEPITAN)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'TEKO PLASTIK-SDG',
                'nama_barang' => 'TEKO PLASTIK (SEDANG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'PAYUNG-25',
                'nama_barang' => 'PAYUNG 25',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'EMBER TUTUP-20',
                'nama_barang' => 'EMBER TUTUP 20',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'KESET MUTIARA',
                'nama_barang' => 'KESET MUTIARA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'KANEBO',
                'nama_barang' => 'KANEBO',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'BAK/BASKOM-20W',
                'nama_barang' => 'BAK/BASKOM 20W',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'SAPU MOBIL',
                'nama_barang' => 'SAPU MOBIL',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'SUTIL STANLIS',
                'nama_barang' => 'SUTIL STANLIS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'KASTOK-TOP',
                'nama_barang' => 'KASTOK (TOP)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPecahBelahId,
                'kode_barang' => 'KASTOK-REN',
                'nama_barang' => 'KASTOK (RENDE)',
                'keterangan' => null,
            ],

            // BISKUIT
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'BETTER-B',
                'nama_barang' => 'BETTER PAM (BESAR)',
                'keterangan' => '@100gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'INTERBIS-NNS',
                'nama_barang' => 'BISKUIT INTERBIS (NANAS)',
                'keterangan' => '@140gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GABIN-MST',
                'nama_barang' => 'GABIN MASTER',
                'keterangan' => '@120gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GABIN-TDS',
                'nama_barang' => 'GABIN TRADISIONAL',
                'keterangan' => '@210gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY-ABN',
                'nama_barang' => 'GERRY ABON',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY-COK',
                'nama_barang' => 'GERRY COKELAT',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY-KJU',
                'nama_barang' => 'GERRY KEJU',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY-KLP',
                'nama_barang' => 'GERRY KELAPA',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY-PDN',
                'nama_barang' => 'GERRY PANDAN',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GOODBIS PEANUT',
                'nama_barang' => 'GOODBIS PEANUT',
                'keterangan' => '@99gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'KUKIS-B',
                'nama_barang' => 'KUKIS (BESAR)',
                'keterangan' => '@218gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MALKIS-ABN',
                'nama_barang' => 'MALKIS ABON',
                'keterangan' => '@105gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MALKIS-B ABN',
                'nama_barang' => 'MALKIS BESAR (ABON)',
                'keterangan' => '@224gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MALKIS-B KOP',
                'nama_barang' => 'MALKIS BESAR (KOPYOR)',
                'keterangan' => '@200gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MALKIS-B MRH',
                'nama_barang' => 'MALKIS BESAR (MERAH)',
                'keterangan' => '@224gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MALKIS-COK',
                'nama_barang' => 'MALKIS COKELAT',
                'keterangan' => '@105gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MALKIS-KJU',
                'nama_barang' => 'MALKIS KEJU',
                'keterangan' => '@105gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MALKIS-MRH',
                'nama_barang' => 'MALKIS MERAH',
                'keterangan' => '@105gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'MARIE SUSU',
                'nama_barang' => 'MARIE SUSU',
                'keterangan' => '@115gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'NABATI-B COK',
                'nama_barang' => 'NABATI WAFER (COKELAT)',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'NABATI-B KJU',
                'nama_barang' => 'NABATI WAFER (KEJU)',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'NABATI-B BRU',
                'nama_barang' => 'NABATI WAFER (PINK LAVA)',
                'keterangan' => '@110gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'OREO-ROLL COK',
                'nama_barang' => 'OREO ROLL (COKELAT)',
                'keterangan' => '@119gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'OREO-ROLL ICE',
                'nama_barang' => 'OREO ROLL (ICE CREAM)',
                'keterangan' => '@119gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'OREO-ROLL KCG',
                'nama_barang' => 'OREO ROLL (KACANG)',
                'keterangan' => '@119gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'OREO-ROLL CREAM',
                'nama_barang' => 'OREO ROLL (PUTIH)',
                'keterangan' => '@119gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'OREO-ROLL STRAW',
                'nama_barang' => 'OREO ROLL (STRAWBERRY)',
                'keterangan' => '@119gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROMA CREAM-COK',
                'nama_barang' => 'ROMA KELAPA CREAM (COKELAT)',
                'keterangan' => '@180gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROMA CREAM-VAN',
                'nama_barang' => 'ROMA KELAPA CREAM (VANILA)',
                'keterangan' => '@180gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROMA-B',
                'nama_barang' => 'ROMA KELAPA MERAH (BESAR)',
                'keterangan' => '@300gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROMA-SAND COK',
                'nama_barang' => 'ROMA SANDWITCH (COKELAT)',
                'keterangan' => '@189gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROMA-SAND KCG',
                'nama_barang' => 'ROMA SANDWITCH (KACANG)',
                'keterangan' => '@189gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROSE CREAM-COK',
                'nama_barang' => 'ROSE CREAM BISKUIT (COKELAT)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROSE CREAM-DUR',
                'nama_barang' => 'ROSE CREAM BISKUIT (DURIAN)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROSE CREAM-KLP',
                'nama_barang' => 'ROSE CREAM BISKUIT (KELAPA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'ROSE CREAM-STRAW',
                'nama_barang' => 'ROSE CREAM BISKUIT (STRAWBERRY)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GANDUM-B',
                'nama_barang' => 'SARI GANDUM (8000)',
                'keterangan' => '@108gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GANDUM-BIG',
                'nama_barang' => 'SARI GANDUM (BIG)',
                'keterangan' => '@240gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GANDUM-K',
                'nama_barang' => 'SARI GANDUM (KECIL)',
                'keterangan' => '@39gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'SUPERCO-B',
                'nama_barang' => 'SUPERCO (BESAR)',
                'keterangan' => '@138gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S COK',
                'nama_barang' => 'TANGGO 5000 (COKELAT)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S KJU',
                'nama_barang' => 'TANGGO 5000 (KEJU)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S MOKA',
                'nama_barang' => 'TANGGO 5000 (MOKA)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S TRM',
                'nama_barang' => 'TANGGO 5000 (TIRAMISU)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S VAN',
                'nama_barang' => 'TANGGO 5000 (VANILA)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'WAFELLO-B COK',
                'nama_barang' => 'WAFELLO (COKELAT)',
                'keterangan' => '@114gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'WAFELLO-B KJU',
                'nama_barang' => 'WAFELLO (KEJU)',
                'keterangan' => '@114gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'WAFELLO-B PDN',
                'nama_barang' => 'WAFELLO (PANDAN)',
                'keterangan' => '@114gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'INTERBIS-KCG',
                'nama_barang' => 'BISKUIT INTERBIS (KACANG)',
                'keterangan' => '@140gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S CCN',
                'nama_barang' => 'TANGGO 5000 (COCONUT)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S PPR',
                'nama_barang' => 'TANGGO 5000 (POPCORN)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'WAFELLO-B CCN',
                'nama_barang' => 'WAFELLO (COCONUT)',
                'keterangan' => '@114gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'WAFELLO-B KRM',
                'nama_barang' => 'WAFELLO (KARAMEL)',
                'keterangan' => '@114gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'TANGGO-S POP',
                'nama_barang' => 'TANGGO 5000 (POPCORN)',
                'keterangan' => '@130gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'CRISPY-CRK',
                'nama_barang' => 'CRISPY CRACKERS (KEJU)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'SLAI OLAI-N',
                'nama_barang' => 'SLAI OLAI (NANAS)',
                'keterangan' => '@192gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'SLAI OLAI-B',
                'nama_barang' => 'SLAI OLAI (BLUEBERRY)',
                'keterangan' => '@192gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'WAFER KRIM-RIA',
                'nama_barang' => 'WAFER KRIM (RIA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY DOUBLE-AB',
                'nama_barang' => 'GERRY DOUBLE ABON',
                'keterangan' => '@90gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY COKELAT-CO',
                'nama_barang' => 'GERRY COKELAT COCONUT',
                'keterangan' => '@90gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY-PAN ',
                'nama_barang' => 'GERRY PANDAN',
                'keterangan' => '@90gr',
            ],
            [
                'id_sub_kategori' => $subKategoriBiskuitId,
                'kode_barang' => 'GERRY-KEJ',
                'nama_barang' => 'GERRY KEJU',
                'keterangan' => '@90gr',
            ],

            // COKELAT BOX
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'ASTOR',
                'nama_barang' => 'ASTOR SINGLE',
                'keterangan' => '@14gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'BENG2',
                'nama_barang' => 'BENG BENG',
                'keterangan' => '@25gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'BENG-MAX',
                'nama_barang' => 'BENG-BENG (MAX)',
                'keterangan' => '@32gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'BETTER-RTG',
                'nama_barang' => 'BETTER (RENTENG)',
                'keterangan' => '@27gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHO2 BAR',
                'nama_barang' => 'CHO2 BAR (BATANG)',
                'keterangan' => '@13gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHO2 MB-STRAW',
                'nama_barang' => 'CHO2 MILK BAR (STRAWBERRY)',
                'keterangan' => '@10gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHO2 ROLL-AST',
                'nama_barang' => 'CHO2 ROLL (ASTOR)',
                'keterangan' => '@7gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHO2 ROLL-BIRU',
                'nama_barang' => 'CHO2 ROLL (BIRU)',
                'keterangan' => '@7gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHO2 PIE NEXTAR',
                'nama_barang' => 'CHOCHO PIE NEXTAR',
                'keterangan' => '@28gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHOCOLATOS-B',
                'nama_barang' => 'CHOCOLATOS (BESAR)',
                'keterangan' => '@14gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHOCOLATOS-K',
                'nama_barang' => 'CHOCOLATOS (KECIL)',
                'keterangan' => '@7gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHOKI-STIK',
                'nama_barang' => 'CHOKI STIK',
                'keterangan' => '@24gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHOKI2',
                'nama_barang' => 'CHOKI-CHOKI',
                'keterangan' => '@9gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'COK-CONE',
                'nama_barang' => 'COKELAT CONE CORONG (TOPLES)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'COK-DILAN',
                'nama_barang' => 'COKELAT DILAN',
                'keterangan' => '@23gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'SUPER-K',
                'nama_barang' => 'COKELAT SUPER (KECIL)',
                'keterangan' => '@16gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'SUPER-B',
                'nama_barang' => 'COKELAT SUPER BESAR (SNAP)',
                'keterangan' => '@28gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'GERRY SALUT KLP-PJG',
                'nama_barang' => 'GERRY SALUT KELAPA (PANJANG)',
                'keterangan' => '@25gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'NEXTAR',
                'nama_barang' => 'NEXTAR',
                'keterangan' => '@31gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'OREO COK',
                'nama_barang' => 'OREO COKELAT 2000',
                'keterangan' => '@36gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'OREO STRAW',
                'nama_barang' => 'OREO STRAWBERY 2000',
                'keterangan' => '@36gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'POCKY',
                'nama_barang' => 'POCKY 5000',
                'keterangan' => '@20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'POCKY-M',
                'nama_barang' => 'POCKY MINI',
                'keterangan' => '@12gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'SOKLAT KRIM-K',
                'nama_barang' => 'SOKLAT KRIM (KECIL)',
                'keterangan' => '@36gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'TOP COKELAT',
                'nama_barang' => 'TOP COKELAT (KECIL))',
                'keterangan' => '@9gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'WINMILK',
                'nama_barang' => 'WINMILK (KIPAS)',
                'keterangan' => '@8gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'CHO2 MB-COK',
                'nama_barang' => 'CHO2 MILK BAR (COKELAT)',
                'keterangan' => '@20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'JOKY-COK',
                'nama_barang' => 'COKELAT JOKY',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'BEAR BISKUIT-COK',
                'nama_barang' => 'COKELAT BEAR BISKUIT',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'PEJOY-COK',
                'nama_barang' => 'PEJOY (COKELAT)',
                'keterangan' => '@30gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'JELLY-COK',
                'nama_barang' => 'JELLY COKELAT (GELAS)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatBoxId,
                'kode_barang' => 'BETTER CRML-RTG',
                'nama_barang' => 'BETTER CARAMEL (RENTENG)',
                'keterangan' => null,
            ],

            // COKELAT CUP
            [
                'id_sub_kategori' => $subKategoriCokelatCupId,
                'kode_barang' => 'CHO2 COLEK',
                'nama_barang' => 'CHO2 COLEK',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatCupId,
                'kode_barang' => 'CHO2 NYAM',
                'nama_barang' => 'CHO2 NYAM',
                'keterangan' => '@15gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatCupId,
                'kode_barang' => 'CHO2-TAL',
                'nama_barang' => 'CHO2 TALI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatCupId,
                'kode_barang' => 'CHO2-M',
                'nama_barang' => 'CHO-CHO EMBER',
                'keterangan' => '@35gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatCupId,
                'kode_barang' => 'NYAM',
                'nama_barang' => 'NYAM-NYAM',
                'keterangan' => '@11gr',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatCupId,
                'kode_barang' => 'CHO2-M 2000',
                'nama_barang' => 'CHO-CHO EMBER',
                'keterangan' => '@2000',
            ],
            [
                'id_sub_kategori' => $subKategoriCokelatCupId,
                'kode_barang' => 'NYAM-B',
                'nama_barang' => 'NYAM-NYAM BESAR',
                'keterangan' => '@25gr',
            ],

            // JAJANAN
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'BOLU KERING-B',
                'nama_barang' => 'BOLU KERING (BESAR)',
                'keterangan' => '@18.000',
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'ILAT SAPI',
                'nama_barang' => 'ILAT SAPI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'KUE BAWANG',
                'nama_barang' => 'KUE BAWANG AAN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'KUE SEMPRONG-B',
                'nama_barang' => 'KUE SEMPRONG/ GAPIT (BESAR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'KUE SEMPRONG-K',
                'nama_barang' => 'KUE SEMPRONG/ GAPIT (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'LAPIS LEGIT',
                'nama_barang' => 'LAPIS LEGIT (TR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'RENGGINAN-BKS',
                'nama_barang' => 'RENGGINAN (BUNGKUS)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'MARIE WIJEN',
                'nama_barang' => 'MARIE WIJEN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'BOLU KERING-K',
                'nama_barang' => 'BOLU KERING (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'ILAT SAPI-BLT',
                'nama_barang' => 'ILAT SAPI BULAT (KUNING)',
                'keterangan' => '@11.000',
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'ILAT SAPI-LJG',
                'nama_barang' => 'ILAT SAPI LONJONG',
                'keterangan' => '@11.000',
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'STIK SAPI',
                'nama_barang' => 'STIK SAPI',
                'keterangan' => '@12.000',
            ],
            [
                'id_sub_kategori' => $subKategoriJajananId,
                'kode_barang' => 'KUE KERING',
                'nama_barang' => 'KUE KERING GULA',
                'keterangan' => null,
            ],

            // JELLY
            [
                'id_sub_kategori' => $subKategoriJellyId,
                'kode_barang' => 'JELLY-GLS',
                'nama_barang' => 'JELLY (HADIAH GELAS)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJellyId,
                'kode_barang' => 'JELLY SAR-GOGONI',
                'nama_barang' => 'JELLY SARIPATI GOGONI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJellyId,
                'kode_barang' => 'JELLY SARIPATI-TOP',
                'nama_barang' => 'JELLY SARIPATI TOPLES',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriJellyId,
                'kode_barang' => 'YOGU STIK',
                'nama_barang' => 'YOGU STIK',
                'keterangan' => null,
            ],

            // PERMEN
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'CHO2 LOLIPOP',
                'nama_barang' => 'CHO-CHO LOLIPOP',
                'keterangan' => '@8gr',
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'CHA2-PJG',
                'nama_barang' => 'COKELAT CHA2 PANJANG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'COK-PAYUNG',
                'nama_barang' => 'COKELAT PAYUNG',
                'keterangan' => '@5gr',
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'CUCU POP',
                'nama_barang' => 'CUCU POP (PERMEN LOLIPOP)',
                'keterangan' => '@9gr',
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'EYEGLASS',
                'nama_barang' => 'EYEGLASS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'KOIN COKELAT',
                'nama_barang' => 'KOIN COKELAT',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'MARSMELLOW-TOPLES',
                'nama_barang' => 'MARSMELLOW (TOPLES)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'MENTOS-ROLL',
                'nama_barang' => 'MENTOS (ROLL)',
                'keterangan' => '@29gr',
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'PARAGO CINCIN',
                'nama_barang' => 'PARAGO CINCIN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'PERMEN BABEL',
                'nama_barang' => 'PERMEN BABEL (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'PERMEN HOT',
                'nama_barang' => 'PERMEN HOT (KAKI)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'PERMEN PUSAN',
                'nama_barang' => 'PERMEN KARET PUSAN (KECIL)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'KISS',
                'nama_barang' => 'PERMEN KISS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'KOPIKO',
                'nama_barang' => 'PERMEN KOPIKO',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'PERMEN LAZERY',
                'nama_barang' => 'PERMEN LAZERY',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'MILKITA 1000',
                'nama_barang' => 'PERMEN MILKITA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'PENDEKAR BIRU',
                'nama_barang' => 'PERMEN PENDEKAR BIRU',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'RELAXA',
                'nama_barang' => 'PERMEN RELAXA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'SMILE TUBE',
                'nama_barang' => 'SMILE TUBE',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'YUPI 1000',
                'nama_barang' => 'YUPI 1000 (ISI 12)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'YUPI 500',
                'nama_barang' => 'YUPI BOX (ISI 24)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'YUPI PERMEN',
                'nama_barang' => 'YUPI PERMEN (ISI 50)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'YUPI SAND',
                'nama_barang' => 'YUPI SANDWICH (ISI 12)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'MENTOS',
                'nama_barang' => 'PERMEN MENTOS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'YUPI BURGER',
                'nama_barang' => 'YUPI BURGER (ISI 12)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'FRUITY GUMY',
                'nama_barang' => 'FRUITY GUMY',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'KAKI PERMEN',
                'nama_barang' => 'KAKI PERMEN (HADIAH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'FRUITY-FIT',
                'nama_barang' => 'FRUITY FIT',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'PERMEN BALL',
                'nama_barang' => 'PERMEN BALL BOLL',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'ALPENLIBLE',
                'nama_barang' => 'PERMEN ALPENLIBLE',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriPermenId,
                'kode_barang' => 'MINT',
                'nama_barang' => 'PERMEN MINT',
                'keterangan' => null,
            ],

            // ROTI
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-COK',
                'nama_barang' => 'ROTI 1000 (COKELAT) #JORDAN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-DUR',
                'nama_barang' => 'ROTI 1000 (DURIAN) #JORDAN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-GPG COK',
                'nama_barang' => 'ROTI 1000 (GEPENG) #COKELAT',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-GPG IJO',
                'nama_barang' => 'ROTI 1000 (GEPENG) #KACANG IJO',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-GPG KLP',
                'nama_barang' => 'ROTI 1000 (GEPENG) #KELAPA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-LPS KTK',
                'nama_barang' => 'ROTI 1000 (LAPIS LEGIT) #KOTAK',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-LPS SGT',
                'nama_barang' => 'ROTI 1000 (LAPIS LEGIT) #SEGITIGA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-NNS',
                'nama_barang' => 'ROTI 1000 (NANAS) #JORDAN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-COK',
                'nama_barang' => 'ROTI AOKA (COKELAT)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-DUR',
                'nama_barang' => 'ROTI AOKA (DURIAN)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-KJU',
                'nama_barang' => 'ROTI AOKA (KEJU)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-NNS',
                'nama_barang' => 'ROTI AOKA (NANAS)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-PDN',
                'nama_barang' => 'ROTI AOKA (PANDAN)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-STRAW',
                'nama_barang' => 'ROTI AOKA (STRAWBERRY)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-VAN',
                'nama_barang' => 'ROTI AOKA (VANILA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-BBR',
                'nama_barang' => 'ROTI AOKA (BLUEBERRY)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-COCOA',
                'nama_barang' => 'ROTI 1000 (COCOA) #BERY',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-SEDAP KTK',
                'nama_barang' => 'ROTI 1000 (SEDAP) #KOTAK',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-SEDAP PJG',
                'nama_barang' => 'ROTI 1000 (SEDAP) #PANJANG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-KLP',
                'nama_barang' => 'ROTI AOKA (KELAPA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI AOKA-MG',
                'nama_barang' => 'ROTI AOKA (MANGGA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI COY PJG-KJU',
                'nama_barang' => 'ROTI COY PANJANG RASA COKELAT-KEJU',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI COY PJG-PSG',
                'nama_barang' => 'ROTI COY PANJANG RASA COKELAT-PISANG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'BOLU NANAS',
                'nama_barang' => 'ROTOI 1000 #BOLU NANAS',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI 1000-GPG VNL',
                'nama_barang' => 'ROTI 1000 (GEPENG) #VANILA',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriRotiId,
                'kode_barang' => 'ROTI SISIR',
                'nama_barang' => 'ROTI SISIR',
                'keterangan' => null,
            ],

            // SNACK 1000
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHIO MIE-AYM',
                'nama_barang' => 'CHIO MIE (AYAM KECAP)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHIO MIE-JAG',
                'nama_barang' => 'CHIO MIE (JAGUNG BAKAR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHIO MIE-PDS',
                'nama_barang' => 'CHIO MIE (PEDAS MANIS)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHIO MIE-LAUT',
                'nama_barang' => 'CHIO MIE (RUMPUT LAUT)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHIO MIE-SAPI',
                'nama_barang' => 'CHIO MIE (SAPI PANGGANG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHO2 CHIPS-K',
                'nama_barang' => 'CHO2 CHIPS (SNACK 1000)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHA2',
                'nama_barang' => 'COKELAT CHA2',
                'keterangan' => '@5gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'DAYAKI-BAKSO',
                'nama_barang' => 'DAYAKI (BAKSO)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'DAYAKI-NASGOR',
                'nama_barang' => 'DAYAKI (NASI GORENG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'DAYAKI-RICA',
                'nama_barang' => 'DAYAKI (RICA-RICA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'DAYAKI-LAUT',
                'nama_barang' => 'DAYAKI (RUMPUT LAUT)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'DAYAKI-IJO',
                'nama_barang' => 'DAYAKI (SAMBAL IJO)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'KERUPUK 1000',
                'nama_barang' => 'KERUPUK KECIL (HARGA 800 @ISI 20) CAMPUR',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'KIKO',
                'nama_barang' => 'KIKO 1000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'MARIE SUSU-RTG',
                'nama_barang' => 'MARIE SUSU (RENTENG)',
                'keterangan' => '@16,5gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'MARSMELLOW-SCT B',
                'nama_barang' => 'MARSMELLOW SACHET (BESAR)',
                'keterangan' => '@6gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'RAINBOW-MINI',
                'nama_barang' => 'RAINBOW MINI POW (1000)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'POPY 1000',
                'nama_barang' => 'SNACK 1000 (POPY)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'RINGBE 1000',
                'nama_barang' => 'SNACK 1000 (RINGBE)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'SUKY 1000',
                'nama_barang' => 'SNACK 1000 (SUKY-SUKY)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'SOSIS AYAM',
                'nama_barang' => 'SOSIS RASA AYAM',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'SOSIS SAPI',
                'nama_barang' => 'SOSIS RASA SAPI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'WAFER CLASIC-RTG',
                'nama_barang' => 'WAFER CLASSIC (RENTENG)',
                'keterangan' => '@18gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'TIC-TAC 1000',
                'nama_barang' => 'TIC-TAC 1000 (CAMPUR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'TOPORO 1000',
                'nama_barang' => 'TOPORO 1000 (CAMPUR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'DAYAKI 1000',
                'nama_barang' => 'DAYAKI 1000 (CAMPUR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHIO MIE 1000',
                'nama_barang' => 'CHIO MIE 1000 (CAMPUR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'PILUS 1000',
                'nama_barang' => 'PILUS 1000 (CAMPUR)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'ENASUKA 1000',
                'nama_barang' => 'ENASUKA 1000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'KWACI 1000',
                'nama_barang' => 'KWACI 1000 (MATAHARI)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'MI BOYKI',
                'nama_barang' => 'SNACK MI BOYKI',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'SPIX 1000',
                'nama_barang' => 'SNACK 1000 (SPIX)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'MIE KREMES',
                'nama_barang' => 'MIE KREMES 1000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'SUAK CRANCY',
                'nama_barang' => 'SUAK CRANCY 1000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'JERUK BALADO',
                'nama_barang' => 'JERUK BALADO',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'POWER F',
                'nama_barang' => 'POWER F 1000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'MARSMELLOW-HOT',
                'nama_barang' => 'MARSHMALLOW HOT DOG',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'TWISBAL',
                'nama_barang' => 'TWISBAL 1000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'UDANG BALADO',
                'nama_barang' => 'UDANG BALADO 1000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack1000Id,
                'kode_barang' => 'CHIO MIE-MALA',
                'nama_barang' => 'CHIO MIE (MALA TRUFFLE)',
                'keterangan' => null,
            ],

            // SNACK 2000
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'CHO2 CHIPS-B',
                'nama_barang' => 'CHO2 CHIPS (2000)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'CRISCITO',
                'nama_barang' => 'CRISCITO',
                'keterangan' => '@20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'GOPEK',
                'nama_barang' => 'GOPEK',
                'keterangan' => '@23gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'KENTANG-K',
                'nama_barang' => 'KENTANG (KECIL)',
                'keterangan' => '@14gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'KUSUKA-K',
                'nama_barang' => 'KUSUKA 2000',
                'keterangan' => '@20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'OPAK-B',
                'nama_barang' => 'OPAK PADANG (BESAR)',
                'keterangan' => '@25gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'TARO-K',
                'nama_barang' => 'TARO (2000)',
                'keterangan' => '@17gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'TATO2',
                'nama_barang' => 'TATO-TATO',
                'keterangan' => '@16gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'TIC-K',
                'nama_barang' => 'TIC-TIC (2000)',
                'keterangan' => '@20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'TOS-K',
                'nama_barang' => 'TOS (2000)',
                'keterangan' => '@20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'TWISKO-K',
                'nama_barang' => 'TWISKO (2000)',
                'keterangan' => '@20gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'USUS 2000',
                'nama_barang' => 'USUS 2000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'KENTANG-S',
                'nama_barang' => 'KENTANG (SEDANG)',
                'keterangan' => 'EXTRA 15%',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'RING',
                'nama_barang' => 'RING 2000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'PIATOS',
                'nama_barang' => 'PIATTOS (2000)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'KUSUKA-S',
                'nama_barang' => 'KUSUKA SEDANG 2000',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'POPCORN',
                'nama_barang' => 'POPCORN',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'CRUNCY',
                'nama_barang' => 'CRUNCY',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack2000Id,
                'kode_barang' => 'CRUNCH -SC',
                'nama_barang' => 'CRUNCH (SEREAL COKELAT)',
                'keterangan' => null,
            ],

            // SNACK 500
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'CHO2-CRS',
                'nama_barang' => 'CHO2 CERES',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'CHO2-CEWEY',
                'nama_barang' => 'CHO2 CEWEY CANDY',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'MARSMELLOW-SCT K',
                'nama_barang' => 'MARSMELLOW SACHET (KECIL)',
                'keterangan' => '@7gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'SIP-K COK',
                'nama_barang' => 'SIP KECIL (COKELAT)',
                'keterangan' => '@4gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'SIP-K KJU',
                'nama_barang' => 'SIP KECIL (KEJU)',
                'keterangan' => '@4gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'SIP-K PDN',
                'nama_barang' => 'SIP KECIL (PANDAN)',
                'keterangan' => '@4gr',
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'BAKSO 500',
                'nama_barang' => 'SNACK 500 (BAKSO "UDANG BAKAR")',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'COCOA 500',
                'nama_barang' => 'SNACK 500 (COCOA CUNCH)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'TENG2 500',
                'nama_barang' => 'SNACK 500 (ES TENG-TENG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'IKUDO 500',
                'nama_barang' => 'SNACK 500 (IKKUDO)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'MIRASA 500',
                'nama_barang' => 'SNACK 500 (MIRASA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'OPAK 500',
                'nama_barang' => 'SNACK 500 (OPAK PADANG)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'PAPA 500',
                'nama_barang' => 'SNACK 500 (PA-PA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'PINK LOVE 500',
                'nama_barang' => 'SNACK 500 (PINK LOVE)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'SPIX 500',
                'nama_barang' => 'SNACK 500 (SPIX)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'YALE 500',
                'nama_barang' => 'SNACK 500 (YALE-YALE)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'ENASUKA 500',
                'nama_barang' => 'SNACK 500 (ENASUKA)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'STIK RUMPUT 500',
                'nama_barang' => 'SNACK 500 (STIK RUMPUT)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'PILOW 500',
                'nama_barang' => 'SNACK 500 (PILOW)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'LUPIN 500',
                'nama_barang' => 'SNACK 500 (LUPIN)',
                'keterangan' => null,
            ],
            [
                'id_sub_kategori' => $subKategoriSnack500Id,
                'kode_barang' => 'OJIKU 500',
                'nama_barang' => 'SNACK 500 (OJIKU)',
                'keterangan' => null,
            ],
        ]);
    }
}
