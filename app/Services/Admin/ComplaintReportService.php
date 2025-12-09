<?php


namespace App\Services\Admin;


use App\Models\Complaint;
use Illuminate\Support\Collection;

class ComplaintReportService
{
    public function generateReport(array $filters = []): Collection
    {
        return Complaint::filter($filters)
            ->get()
            ->map(function ($complaint) {
                return [
                    'رقم الشكوى'     => $complaint->reference_number,
                    'اسم المواطن'    => $complaint->user->first_name . ' ' . $complaint->user->last_name,
                    'الجهة الحكومية' => $complaint->organization->name ?? 'غير محددة',
                    'نوع الشكوى'     => $complaint->type,
                    'عنوان الشكوى'   => $complaint->title,
                    'الحالة'         => $complaint->status,
                    'الموقع'         => $complaint->location,
                    'تاريخ الإنشاء'  => $complaint->created_at->format('Y-m-d'),
                ];
            });
    }
}
