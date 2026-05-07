<table>
    <thead>
    <tr>
        <th colspan="2"><strong>Nama: {{ $user->name }}</strong></th>
        <th></th>
        <th align="right"><strong>{{ $monthName }} {{ $year }}</strong></th>
    </tr>
    <tr></tr>
    <tr>
        <th style="border: 1px solid #000; background-color: #E2E8F0;"><strong>Hari/Tanggal</strong></th>
        <th style="border: 1px solid #000; background-color: #E2E8F0;"><strong>Project</strong></th>
        <th style="border: 1px solid #000; background-color: #E2E8F0;"><strong>Aktifitas</strong></th>
        <th style="border: 1px solid #000; background-color: #E2E8F0;"><strong>Deskripsi</strong></th>
    </tr>
    </thead>
    <tbody>
    @foreach($activities as $activity)
        <tr>
            <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($activity->date)->translatedFormat('l, d F Y H:i') }}</td>
            <td style="border: 1px solid #000;">{{ $activity->project->name ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $activity->title }}</td>
            <?php
                // Convert markdown to plain text for Excel export (strip HTML to keep cells clean)
                $descHtml = \Illuminate\Support\Str::markdown($activity->description ?? '');
                $descText = trim(strip_tags($descHtml));
                // collapse multiple whitespace/newlines
                $descText = preg_replace('/\s+/', ' ', $descText);
            ?>
            <td style="border: 1px solid #000;">{{ $descText }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
