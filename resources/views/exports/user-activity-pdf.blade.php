<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 20px; }
        .header-content { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #4F46E5; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #4F46E5; }
        .user-info { font-size: 14px; font-weight: bold; }
        .period { text-align: right; font-size: 14px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #F8FAFC; border: 1px solid #CBD5E1; padding: 10px; text-align: left; font-weight: bold; color: #1E293B; }
        td { border: 1px solid #CBD5E1; padding: 8px; text-align: left; vertical-align: top; }
        tr:nth-child(even) { background-color: #F1F5F9; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #64748B; }
    </style>
</head>
<body>
    <div class="header">
        <div style="float: left;" class="user-info">Nama: {{ $user->name }}</div>
        <div style="float: right;" class="period">{{ $monthName }} {{ $year }}</div>
        <div style="clear: both;"></div>
        <div style="margin-top: 5px; height: 2px; background-color: #4F46E5;"></div>
    </div>

    <h2 style="text-align: center; color: #1E293B;">Laporan Aktivitas Harian</h2>

    <table>
        <thead>
            <tr>
                <th width="20%">Hari/Tanggal</th>
                <th width="15%">Project</th>
                <th width="25%">Aktifitas</th>
                <th width="40%">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $activity)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($activity->date)->translatedFormat('l, d F Y H:i') }}</td>
                    <td>{{ $activity->project->name ?? '-' }}</td>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Tidak ada data kegiatan ditemukan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
