<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Struktur;
use App\Models\Korwil;
use Illuminate\Support\Str;
class PopulateRoleIds extends Command {
    protected $signature = 'app:populate-role-ids';
    protected $description = 'Populate the new role_id column in struktur and korwil tables';
    public function handle() {
        $this->info('Memulai pengisian role_id...');
        $roles = Role::all()->keyBy('name');

        Struktur::chunk(200, function ($strukturs) use ($roles) {
            foreach ($strukturs as $struktur) {
                $tingkatanLabel = (strtolower($struktur->tingkat) === 'dpac') ? 'PAC' : 'Ranting';
                $roleName = Str::ucfirst(strtolower($struktur->jabatan)) . ' ' . $tingkatanLabel;
                if (isset($roles[$roleName])) {
                    $struktur->role_id = $roles[$roleName]->id;
                    $struktur->save();
                }
            }
        });
        $this->info('role_id untuk tabel struktur selesai diisi.');

        Korwil::chunk(200, function ($korwils) use ($roles) {
            foreach ($korwils as $korwil) {
                $roleName = strtoupper($korwil->tingkat);
                if (isset($roles[$roleName])) {
                    $korwil->role_id = $roles[$roleName]->id;
                    $korwil->save();
                }
            }
        });
        $this->info('role_id untuk tabel korwil selesai diisi.');
        return 0;
    }
}