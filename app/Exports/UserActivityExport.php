<?php

namespace App\Exports;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class UserActivityExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize, WithStyles
{
    public function __construct(
        public int $userId,
        public string $month,
        public string $year
    ) {}

    /**
     * Rows prepared for export (kept for AfterSheet event)
     *
     * @var Collection<int, array>
     */
    private Collection $rows;

    public function collection(): Collection
    {
        Carbon::setLocale('id');

        $activities = Activity::with('project')
            ->where('user_id', $this->userId)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->orderBy('date')
            ->get();

        $this->rows = $activities->map(function (Activity $a) {
            $descHtml = Str::markdown($a->description ?? '');
            $descText = trim(strip_tags($descHtml));
            $descText = preg_replace('/\s+/', ' ', $descText);

            return [
                'date' => Carbon::parse($a->date)->translatedFormat('l, d F Y H:i'),
                'project' => $a->project->name ?? '-',
                'title' => $a->title,
                'description' => $descText,
                'image_path' => $this->extractImagePath($a->description ?? ''),
            ];
        });

        return new Collection($this->rows->map(fn($r) => [
            $r['date'], $r['project'], $r['title'], $r['description'],
        ]));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'Hari/Tanggal',
            'Project',
            'Aktifitas',
            'Deskripsi',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Starting row for data (headings are on row 1)
                $startRow = 2;

                foreach ($this->rows->values() as $index => $row) {
                    $excelRow = $startRow + $index;

                    // If image exists, insert it into column D (4)
                    if (! empty($row['image_path']) && file_exists($row['image_path'])) {
                        $drawing = new Drawing();
                        $drawing->setPath($row['image_path']);
                        $drawing->setCoordinates('D' . $excelRow);
                        $drawing->setHeight(80);
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWorksheet($sheet);
                    }
                }
            },
        ];
    }

    /**
     * Try to extract first image URL from markdown and map to local file path if possible.
     */
    private function extractImagePath(string $markdown): ?string
    {
        // match markdown image: ![alt](url)
        if (preg_match('/!\[[^\]]*\]\(([^)\s]+)(?:\s+"[^"]*")?\)/i', $markdown, $m)) {
            $url = $m[1];
        } else {
            // try html <img src="...">
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $markdown, $m)) {
                $url = $m[1];
            } else {
                return null;
            }
        }

        // normalize protocol-relative
        if (strpos($url, '//') === 0) {
            $url = (config('app.url') ? rtrim(config('app.url'), '/') : 'http:') . $url;
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? null;

        if ($path && (str_starts_with($path, '/storage') || str_contains($path, '/storage/'))) {
            $local = public_path(ltrim($path, '/'));
            if (file_exists($local)) {
                return $local;
            }
        }

        return null;
    }

    private function getMonthName(string $month): string
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        return $months[$month] ?? '';
    }
}
