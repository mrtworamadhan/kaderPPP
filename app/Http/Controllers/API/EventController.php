<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\PoinLog;
use App\Models\Role;
use App\Models\Struktur;
use App\Models\Korwil;
use App\Models\Anggota;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    use ApiResponder;

    public function getRoles()
    {
        return $this->success(Role::orderBy('name')->get(), 'Daftar role berhasil diambil.');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'required|string|max:255',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'points_reward' => 'required|integer|min:0',
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id',
            ])->validate();

            $validatedData['qr_code_token'] = Str::random(32);
            $event = Event::create($validatedData);
            $event->roles()->attach($validatedData['roles']);
            return $this->success($event->load('roles'), 'Agenda baru berhasil dibuat.', 201);
        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan saat membuat agenda.', 500);
        }
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('User tidak terautentikasi.', 401);
        }

        // --- INI PERBAIKANNYA ---
        // Coba ambil profil anggota melalui relasi.
        $anggota = $user->anggota;

        // Jika relasi gagal (karena data inkonsisten), coba cari manual via NIK.
        if (!$anggota) {
            $anggota = Anggota::where('nik', $user->nik)->first();
        }

        // Jika setelah dicari manual tetap tidak ada, baru kita menyerah.
        if (!$anggota) {
            return $this->success(['berlangsung' => [], 'akan_datang' => []], 'Tidak ada profil anggota yang cocok untuk user ini.');
        }

        $anggotaId = $anggota->id;
        $userRoleNames = [];

        // 1. Role dasar
        $userRoleNames['Seluruh Anggota & Kader Partai'] = true;

        // 2. Cek jabatan di tabel struktur
        $strukturJabatan = Struktur::where('anggota_id', $anggotaId)->get();
        if ($strukturJabatan->isNotEmpty()) {
            $userRoleNames['Seluruh Tingkatan Pengurus'] = true;
            foreach ($strukturJabatan as $struktur) {
                $tingkatanLabel = (strtolower($struktur->tingkat) === 'dpac') ? 'PAC' : 'Ranting';
                $roleName = Str::ucfirst(strtolower($struktur->jabatan)) . ' ' . $tingkatanLabel;
                $userRoleNames[$roleName] = true;
            }
        }

        // 3. Cek jabatan di tabel korwil
        $korwilJabatan = Korwil::where('anggota_id', $anggotaId)->get();
        if ($korwilJabatan->isNotEmpty()) {
            $userRoleNames['Seluruh Tingkatan Pengurus'] = true;
            foreach ($korwilJabatan as $korwil) {
                $userRoleNames[strtoupper($korwil->tingkat)] = true;
            }
        }

        // 4. Ambil ID dari semua role yang dimiliki kader
        $roleIds = Role::whereIn('name', array_keys($userRoleNames))->pluck('id');

        // 5. Cari event yang ditujukan untuk role-role tersebut
        $now = Carbon::now();
        $eventsQuery = Event::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('role_id', $roleIds->unique()->all());
        });

        $data = [
            'berlangsung' => (clone $eventsQuery)->where('start_time', '<=', $now)->where('end_time', '>=', $now)->orderBy('start_time', 'asc')->get(),
            'akan_datang' => (clone $eventsQuery)->where('start_time', '>', $now)->orderBy('start_time', 'asc')->get(),
        ];

        return $this->success($data, 'Daftar agenda berhasil diambil.');
    }

    public function showQrCode(Event $event)
    {
        if (empty($event->qr_code_token)) {
            return $this->error('Event ini tidak memiliki token QR.', 404);
        }

        // Data yang akan di-encode di dalam QR code
        // Kita buat dalam format JSON agar mudah dibaca oleh scanner
        $qrData = json_encode([
            'event_id' => $event->id,
            'token' => $event->qr_code_token,
        ]);

        // Generate QR code sebagai gambar SVG
        $svg = QrCode::size(400)->format('svg')->generate($qrData);

        // Kirim gambar sebagai respons
        return Response::make($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function attend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        $anggota = $user->anggota;

        if (!$anggota) {
            return $this->error('Profil anggota tidak ditemukan.', 404);
        }

        // Cari event berdasarkan token dari QR code
        $event = Event::where('qr_code_token', $request->qr_code_token)->first();

        if (!$event) {
            return $this->error('Agenda tidak valid atau tidak ditemukan.', 404);
        }

        // Cek apakah agenda sedang berlangsung
        $now = now();
        if (!($now->between($event->start_time, $event->end_time))) {
            return $this->error('Absensi untuk agenda ini belum dibuka atau sudah ditutup.', 403);
        }

        // Cek apakah kader sudah pernah absen
        $alreadyAttended = EventAttendance::where('event_id', $event->id)
            ->where('anggota_id', $anggota->id)
            ->exists();
        if ($alreadyAttended) {
            return $this->error('Anda sudah tercatat hadir di agenda ini.', 409); // 409 Conflict
        }

        // Jika semua validasi lolos, catat kehadiran dan berikan poin
        try {
            DB::beginTransaction();

            // 1. Catat kehadiran
            EventAttendance::create([
                'event_id' => $event->id,
                'anggota_id' => $anggota->id,
            ]);

            // 2. Berikan poin jika ada
            if ($event->points_reward > 0) {
                $anggota->total_poin += $event->points_reward;
                $anggota->save();

                // 3. Catat di log poin
                PoinLog::create([
                    'anggota_id' => $anggota->id,
                    'event_id' => $event->id,
                    'points' => $event->points_reward,
                    'description' => 'Kehadiran di agenda: ' . $event->title,
                ]);
            }

            DB::commit();

            return $this->success([
                'event_title' => $event->title,
                'points_earned' => $event->points_reward,
            ], 'Kehadiran berhasil dicatat. Selamat datang!');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Terjadi kesalahan internal saat mencatat kehadiran.', 500);
        }
    }

    public function adminIndex()
    {
        $events = Event::withCount('attendances')->latest()->get();

        $events->transform(function ($event) {
            // Menghitung jumlah peserta yang diundang (ini bisa menjadi query yang berat)
            // Untuk sekarang kita hitung secara sederhana, nanti bisa dioptimalkan
            $roleIds = $event->roles->pluck('id');
            $invitedCount = $this->calculateInvitedCount($roleIds);
            
            $event->invited_count = $invitedCount;
            $event->attendance_percentage = $invitedCount > 0 ? round(($event->attendances_count / $invitedCount) * 100) : 0;
            return $event;
        });

        $now = now();
        $data = [
            // --- INI PERBAIKANNYA: Tambahkan ->values() ---
            'selesai' => $events->where('end_time', '<', $now)->values(),
            'berlangsung' => $events->where('start_time', '<=', $now)->where('end_time', '>=', $now)->values(),
            'akan_datang' => $events->where('start_time', '>', $now)->values(),
        ];

        return $this->success($data, 'Rekapitulasi agenda untuk admin berhasil diambil.');
    }

    /**
     * [ADMIN] Mengambil daftar peserta yang hadir di sebuah event.
     */
    public function getAttendees(Event $event)
    {
        $attendees = $event->attendances()->with('anggota:id,nik,nama,phone')->get()->pluck('anggota');
        return $this->success($attendees, 'Daftar peserta hadir berhasil diambil.');
    }

    private function calculateInvitedCount($roleIds)
    {
        $roles = Role::whereIn('id', $roleIds)->get();
        $processedAnggotaIds = [];

        foreach ($roles as $role) {
            $query = null;
            $roleName = $role->name;

            if ($roleName === 'Seluruh Anggota & Kader Partai') {
                return Anggota::count();
            }
            
            if ($roleName === 'Seluruh Tingkatan Pengurus') {
                 $query = Anggota::where(function($q) {
                    $q->has('struktur')->orHas('korwil');
                });
            } else if (str_contains($roleName, 'PAC') || str_contains($roleName, 'Ranting')) {
                [$jabatan, $tingkat] = explode(' ', $roleName, 2);
                $tingkat = str_contains($tingkat, 'PAC') ? 'dpac' : 'dprt';
                $query = Anggota::whereHas('struktur', function($q) use ($jabatan, $tingkat){
                    $q->where('jabatan', $jabatan)->where('tingkat', $tingkat);
                });
            } else if ($roleName === 'KORW' || $roleName === 'KORT') {
                $query = Anggota::whereHas('korwil', function($q) use ($roleName){
                    $q->where('tingkat', strtolower($roleName));
                });
            }
            
            if ($query) {
                $ids = $query->whereNotIn('id', $processedAnggotaIds)->pluck('id')->all();
                $processedAnggotaIds = array_merge($processedAnggotaIds, $ids);
            }
        }
        return count(array_unique($processedAnggotaIds));
    }

}
