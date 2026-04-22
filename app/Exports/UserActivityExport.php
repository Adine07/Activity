<?php

namespace App\Exports;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserActivityExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(
        public int $userId,
        public string $month,
        public string $year
    ) {}

    public function view(): View
    {
        Carbon::setLocale('id');

        $activities = Activity::with('project')
            ->where('user_id', $this->userId)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->orderBy('date')
            ->get();

        $user = User::find($this->userId);
        $monthName = $this->getMonthName($this->month);

        return view('exports.user-activity-excel', [
            'activities' => $activities,
            'user' => $user,
            'monthName' => $monthName,
            'year' => $this->year,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
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
