<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\User;
use App\Models\Struktur;
use Illuminate\Support\Facades\Hash;

class AnggotaDpcSeeder extends Seeder
{
    public function run()
    {
        Anggota::truncate();
        Struktur::truncate();
        User::where('role', 'anggota')->delete();
        $data = [
            ["tingkatan" => "dpc", "nik" => "3201142908740000", "nama" => "Dede Candra Sasmita., S.Ag., M.Pd., M.H", "jabatan" => "Ketua", "bagian" => "Pengurus Harian"],
            ["tingkatan" => "dpc", "nik" => "3171080501730003", "nama" => "Saiful Amari., S.T", "jabatan" => "Wakil Ketua 1", "bagian" => "Pengurus Harian"],
            ["tingkatan" => "dpc", "nik" => "3201170204830003", "nama" => "Ruhiyat Sujana", "jabatan" => "Wakil Ketua 3", "bagian" => "Pengurus Harian"],
            ["tingkatan" => "dpc", "nik" => "3201161408730001", "nama" => "H. Haryanto Surbakti., S.H.", "jabatan" => "Sekretaris", "bagian" => "Pengurus Harian"],
            ["tingkatan" => "dpc", "nik" => "3201010501840018", "nama" => "Abdur Rozak., S.Pd", "jabatan" => "Wakil Sekretaris 1", "bagian" => "Pengurus Harian"],
            ["tingkatan" => "dpc", "nik" => "3201152808930002", "nama" => "Fauzul Bayan Tajudin., SE", "jabatan" => "Wakil Sekretaris 2", "bagian" => "Pengurus Harian"],
            ["tingkatan" => "dpc", "nik" => "3201012209760013", "nama" => "Muhammad Ardan., S.E", "jabatan" => "Direktur Eksekutif", "bagian" => "DirEks"],
            ["tingkatan" => "dpc", "nik" => "3201021008740011", "nama" => "Dedi Mulyadi", "jabatan" => "Anggota", "bagian" => "DirEks"],
            ["tingkatan" => "dpc", "nik" => "3201012108800006", "nama" => "Fitriyanto Riyadi", "jabatan" => "Anggota", "bagian" => "DirEks"],
            ["tingkatan" => "dpc", "nik" => "3201010812700009", "nama" => "Dirhalamsyah Siregar", "jabatan" => "Anggota", "bagian" => "DirEks"],
            ["tingkatan" => "dpc", "nik" => "3201340110750001", "nama" => "Irman Nurcahyan., S.E", "jabatan" => "Wakil Bendahara 1", "bagian" => "Pengurus Harian"],
            ["tingkatan" => "dpc", "nik" => "3201030105950001", "nama" => "Taopik Hidayat", "jabatan" => "Wakil Kepala Badan", "bagian" => "BAPPILU"],
            ["tingkatan" => "dpc", "nik" => "3201030703070029", "nama" => "Rusdiantoro", "jabatan" => "Sekretaris", "bagian" => "BAPPILU"],
            ["tingkatan" => "dpc", "nik" => "3201021306650006", "nama" => "Cecep Jaelani", "jabatan" => "Anggota", "bagian" => "BAPPILU"],
            ["tingkatan" => "dpc", "nik" => "3201191010640001", "nama" => "Saripudin", "jabatan" => "Anggota", "bagian" => "BAPPILU"],
            ["tingkatan" => "dpc", "nik" => "3201161011980001", "nama" => "Amarulloh Surbakti., S.H", "jabatan" => "Anggota", "bagian" => "BAPPILU"],
            ["tingkatan" => "dpc", "nik" => "3201183009960001", "nama" => "Ibnu Sakti Mubarok., S.Hum", "jabatan" => "Kepala Badan", "bagian" => "BPOKK"],
            ["tingkatan" => "dpc", "nik" => "3202011112900013", "nama" => "Dedi Andriansyah, S.H", "jabatan" => "Wakil Kepala Badan", "bagian" => "BPOKK"],
            ["tingkatan" => "dpc", "nik" => "3172012109990005", "nama" => "Wildan Muholad., S.M.", "jabatan" => "Sekretaris", "bagian" => "BPOKK"],
            ["tingkatan" => "dpc", "nik" => "3201291302940002", "nama" => "Muhammad Rizqi Ramadhan, S.H.", "jabatan" => "Anggota", "bagian" => "BPOKK"],
            ["tingkatan" => "dpc", "nik" => "3201130511770003", "nama" => "Halimih Yudistira., S.Th.I., M.Si", "jabatan" => "Anggota", "bagian" => "BPOKK"],
            ['tingkatan' => 'dpc', 'nik' => '3201032907790003', 'nama' => 'Ade Ahmad Mubarok., S.Th.I., S.E., M.A.', 'jabatan' => 'Kepala Bidang', 'bagian' => 'BALITBANG', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201291911830009', 'nama' => 'Ricky Fedriansyah., SE', 'jabatan' => 'Sekretaris', 'bagian' => 'BALITBANG', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201014403820003', 'nama' => 'Naotalia Apapyo., S.Psi., S.Keb., BDN', 'jabatan' => 'Wakil Ketua 2', 'bagian' => 'Pengurus Harian', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201157006910005', 'nama' => 'Putri Aldilla Fanisha Fauzi., S.T', 'jabatan' => 'Wakil Sekretaris 3', 'bagian' => 'Pengurus Harian', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201385602050001', 'nama' => 'Riska Amelia', 'jabatan' => 'Wakil Sekretaris 4', 'bagian' => 'Pengurus Harian', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201014707820002', 'nama' => 'Naih', 'jabatan' => 'Anggota', 'bagian' => 'DirEks', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201015307610004', 'nama' => 'Hj Atty Ruhiyati., S.IP', 'jabatan' => 'Bendahara', 'bagian' => 'Pengurus Harian', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201384403860004', 'nama' => 'Ratu R. Zahriah, S.Pd', 'jabatan' => 'Kepala Badan', 'bagian' => 'BAPPILU', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201015612960002', 'nama' => 'Dea Destiara', 'jabatan' => 'Anggota', 'bagian' => 'BPOKK', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201095204810008', 'nama' => 'Elis Nursamsiah., S.Pd.I., MM', 'jabatan' => 'Anggota', 'bagian' => 'BALITBANG', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3603287112850002', 'nama' => 'Nursyah Depiana', 'jabatan' => 'Anggota', 'bagian' => 'BADIKLAT', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201166712880001', 'nama' => 'Mega Lina Nour', 'jabatan' => 'Anggota', 'bagian' => 'BADIKLAT', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201135206910001', 'nama' => 'Andriyanti Wijaya', 'jabatan' => 'Anggota', 'bagian' => 'BAKOMSTRA', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201144103930005', 'nama' => 'Fauziah Ambar Mayang.,  S.T.', 'jabatan' => 'Anggota', 'bagian' => 'BPJK', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201136208750003', 'nama' => 'Virgandynie Eh Toto', 'jabatan' => 'Anggota', 'bagian' => 'BPPM', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201145406890001', 'nama' => 'Yuni Rahayu', 'jabatan' => 'Anggota', 'bagian' => 'BPPM', 'desa' => null, 'kecamatan' => null],
            ['tingkatan' => 'dpc', 'nik' => '3201017112740013', 'nama' => 'Yayah Asmariah', 'jabatan' => 'Anggota', 'bagian' => 'BPPM', 'desa' => null, 'kecamatan' => null],
            ["tingkatan" => "dpc", "nik" => "3201025606830022", "nama" => "Laras", "jabatan" => "Anggota", "bagian" => "BHPP"],
            ["tingkatan" => "dpc", "nik" => "3201240501620001", "nama" => "Nanang Suryaman., S,Sos", "jabatan" => "Anggota", "bagian" => "BALITBANG"],
            ["tingkatan" => "dpc", "nik" => "3201250304670002", "nama" => "Yayan Nuryana., S.Pd", "jabatan" => "Anggota", "bagian" => "BALITBANG"],
            ["tingkatan" => "dpc", "nik" => "3201010306770020", "nama" => "Irwan Setiawan", "jabatan" => "Kepala Bidang", "bagian" => "BADIKLAT"],
            ["tingkatan" => "dpc", "nik" => "3201231010750003", "nama" => "Anas Zamzami., S.Ag", "jabatan" => "Sekretaris", "bagian" => "BADIKLAT"],
            ["tingkatan" => "dpc", "nik" => "3201011701860013", "nama" => "Hendra Kurniawan., S.Pd", "jabatan" => "Anggota", "bagian" => "BADIKLAT"],
            ["tingkatan" => "dpc", "nik" => "3201301505910001", "nama" => "Gita Purnama", "jabatan" => "Kepala Bidang", "bagian" => "BAKOMSTRA"],
            ["tingkatan" => "dpc", "nik" => "3201333008020004", "nama" => "Subhanudin al Farizi", "jabatan" => "Anggota", "bagian" => "BAKOMSTRA"],
            ["tingkatan" => "dpc", "nik" => "3201270705000005", "nama" => "Muhammad Aulia Rahman., S.I.P", "jabatan" => "Kepala Bidang", "bagian" => "BPJK"],
            ["tingkatan" => "dpc", "nik" => "3201182205000002", "nama" => "Dandy Arland Nugraha., S.S.", "jabatan" => "Sekretaris", "bagian" => "BPJK"],
            ["tingkatan" => "dpc", "nik" => "3201032502000006", "nama" => "Nurmansyah., S.H", "jabatan" => "Anggota", "bagian" => "BPJK"],
            ["tingkatan" => "dpc", "nik" => "3201190205670001", "nama" => "Agus Awaludin", "jabatan" => "Anggota", "bagian" => "BPJK"],
            ["tingkatan" => "dpc", "nik" => "3201392308960003", "nama" => "Tamamul Fikri", "jabatan" => "Anggota", "bagian" => "BPJK"],
            ["tingkatan" => "dpc", "nik" => "6371012110760006", "nama" => "Sentang Nainggolan", "jabatan" => "Kepala Bidang", "bagian" => "BPPM"],
            ["tingkatan" => "dpc", "nik" => "3201021201670006", "nama" => "M. Thorop Hutabarat", "jabatan" => "Sekretaris", "bagian" => "BPPM"],
            ["tingkatan" => "dpc", "nik" => "3201020607770022", "nama" => "Aman Sulaeman", "jabatan" => "Anggota", "bagian" => "BPPM"],
            ["tingkatan" => "dpc", "nik" => "3173070205640008", "nama" => "H. Afdal Affan., S.H.", "jabatan" => "Kepala Bidang", "bagian" => "BHPP"],
            ["tingkatan" => "dpc", "nik" => "3204061604890000", "nama" => "Gerry Ramadhan., S.Sy.", "jabatan" => "Sekretaris", "bagian" => "BHPP"],
            ["tingkatan" => "dpc", "nik" => "3201010404660008", "nama" => "T. Erwin Freddy Lumbantoruan., S.T.", "jabatan" => "Anggota", "bagian" => "BHPP"],

        ];

        $prefix = '909013201';
        $counter = 1;
        
        foreach ($data as $item) {
            Anggota::create([
                'nik' => $item['nik'],
                'id_anggota' => $prefix . str_pad($counter, 5, '0', STR_PAD_LEFT),
                'nama' => $item['nama'],
                'desa' => null,
                'id_desa' => null,
                'kecamatan' => null,
                'id_kecamatan' => null,
                'jabatan' => $item['tingkatan'],
            ]);

            Struktur::create([
                'tingkat' => $item['tingkatan'],
                'nik' => $item['nik'],
                'nama' => $item['nama'],
                'jabatan' => $item['jabatan'],
                'bagian' => $item['bagian'],
                'urutan' => null,
                'desa' => null,
                'id_desa' => null,
                'kecamatan' => null,
                'id_kecamatan' => null,
            ]);

            User::create([
                'nik' => $item['nik'],
                'password' => Hash::make('demokratjuara'),
                'role' => 'anggota',
            ]);

            $counter++;
        }
    }
}
