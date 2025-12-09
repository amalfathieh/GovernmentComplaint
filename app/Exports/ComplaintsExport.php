<?php

namespace App\Exports;
namespace App\Exports;

use App\Models\Complaint;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ComplaintsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Complaint::query()->filter($this->filters)->with(['user', 'organization']);
    }

    public function headings(): array
    {
        return [
            'رقم الشكوى',
            'اسم المواطن',
            'الجهة الحكومية',
            'نوع الشكوى',
            'عنوان الشكوى',
            'الحالة',
            'الموقع',
            'تاريخ الإنشاء',
        ];
    }

    public function map($complaint): array
    {
        return [
            $complaint->reference_number,
            $complaint->user ? $complaint->user->first_name . ' ' . $complaint->user->last_name : 'غير معروف',
            $complaint->organization ? $complaint->organization->name : 'غير محددة',
            $complaint->type,
            $complaint->title,
            $complaint->status,
            $complaint->location,
            $complaint->created_at->format('Y-m-d'),
        ];
    }
}
