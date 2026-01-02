<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\ComplaintsExport;
use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Services\Admin\ComplaintReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportController extends Controller
{

    public function exportComplaintsXlsx(Request $request, ComplaintReportService $reportService)
    {
        try {
            $filters = request()->only(['status', 'organization_id', 'from', 'to']);
            $fileName = 'complaints_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            return Excel::download(new ComplaintsExport($filters), $fileName);
        } catch (\Exception $e) {
            Log::error("Fail to Export xlsx: " . $e->getMessage());
            return Response::Error($e->getMessage(), 403);
        }
    }

    public function exportComplaintsPdf(Request $request, ComplaintReportService $reportService)
    {
        try {
            $complaints = $reportService->generateReport(request()->only(['status', 'organization_id', 'from', 'to']));


            $pdf = Pdf::loadView('reports.complaints_pdf', compact('complaints'))
                ->setPaper('A4', 'portrait');

            return response()->streamDownload(
                fn() => print($pdf->output()),
                'complaints_report_' . now()->format('Y_m_d_H_i_s') . '.pdf',
                [
                    'Content-Type' => 'application/pdf',
                ]
            );

        } catch (\Exception $e) {
            Log::error("PDF Stream Error: " . $e->getMessage());
            return Response::Error($e->getMessage(), 403);
        }
    }

    public function exportComplaintsCsv()
    {

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['reference_number', 'citizenName', 'organization_name', 'type','title', 'status', 'location', 'date']);

            Complaint::chunk(500, function ($complaints) use ($handle) {
                foreach ($complaints as $c) {
                    fputcsv($handle, [
                        $c->reference_number,
                        $c->user->first_name . ' ' . $c->user->last_name,
                        $c->organization->name ?? 'غير محددة',
                        $c->type,
                        $c->title,
                        $c->status,
                        $c->location,
                        $c->created_at->format('Y-m-d'),
                    ]);
                }
            });

            fclose($handle);
        }, 'complaints.csv');

    }
}
