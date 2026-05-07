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
        /* Description content styling */
        .desc { word-wrap: break-word; max-width: 100%; }
        .desc img { max-width: 100%; height: auto; display: block; max-height: 180px; margin-top:6px; }
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
                    <td class="desc">
                        <?php
                            $raw = $activity->description ?? '';
                            $html = \Illuminate\Support\Str::markdown($raw);

                            // Replace image URLs that point to storage/public with local file paths so DomPDF can render them
                            $html = preg_replace_callback('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', function ($m) {
                                $url = $m[1];
                                // handle protocol-relative URLs
                                if (strpos($url, '//') === 0) {
                                    $url = (config('app.url') ? rtrim(config('app.url'), '/') : 'http:') . $url;
                                }

                                $parsed = parse_url($url);
                                $path = $parsed['path'] ?? null;

                                // If image is served from /storage or /uploads, map to public_path
                                if ($path && (str_starts_with($path, '/storage') || str_contains($path, '/storage/'))) {
                                    $local = public_path(ltrim($path, '/'));
                                    if (file_exists($local)) {
                                        $new = 'file://' . $local;
                                        return str_replace($m[1], $new, $m[0]);
                                    }
                                }

                                // if cannot map, return original tag
                                return $m[0];
                            }, $html);

                            // Output rendered HTML (safe because from markdown input)
                            echo $html;
                        ?>
                    </td>
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
