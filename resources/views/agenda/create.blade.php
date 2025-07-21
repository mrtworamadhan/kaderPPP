<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tambah Agenda Baru</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 40px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input,
        textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Tambah Agenda Baru</h1>

    <form action="{{ route('agenda.store') }}" method="POST">
        @csrf <div class="form-group">
            <label for="title">Judul Agenda</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="location">Lokasi</label>
            <input type="text" name="location" id="location" required>
        </div>
        <div class="form-group">
            <label for="start_time">Waktu Mulai</label>
            <input type="datetime-local" name="start_time" id="start_time" required>
        </div>
        <div class="form-group">
            <label for="end_time">Waktu Selesai</label>
            <input type="datetime-local" name="end_time" id="end_time" required>
        </div>
        <div class="form-group">
            <label>Target Peserta (Jabatan)</label>
            @foreach ($roles as $role)
                <div>
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                    <label for="role_{{ $role->id }}">{{ $role->name }}</label>
                </div>
            @endforeach
        </div>
        <button type="submit">Simpan Agenda</button>
    </form>
</body>

</html>