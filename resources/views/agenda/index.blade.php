<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Agenda Kegiatan</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .event-item { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        h3 { margin-top: 0; }
    </style>
</head>
<body>

    <h1>Daftar Agenda Kegiatan</h1>
    @if (session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif
    <a href="{{ route('agenda.create') }}" style="display:inline-block; margin-bottom: 20px;">+ Tambah Agenda Baru</a>
    <hr>

    @forelse ($events as $event)
        <div class="event-item">
            <h3>{{ $event->title }}</h3>
            <p>Lokasi: {{ $event->location }}</p>
            <p>Waktu: {{ $event->start_time->format('d M Y, H:i') }} WIB</p>
        </div>
    @empty
        <p>Belum ada agenda kegiatan yang dijadwalkan.</p>
    @endforelse

</body>
</html>