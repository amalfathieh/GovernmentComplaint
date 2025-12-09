<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\ComplaintReportExport;
use App\Exports\ComplaintsExport;
use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Services\Admin\ComplaintReportService;
use Illuminate\Http\Request;
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
            return Response::Error($e->getMessage(), 403);
        }
    }

    public function exportComplaintsPdf(Request $request, ComplaintReportService $reportService)
    {
        try {
            $complaints = $reportService->generateReport(request()->only(['status', 'organization_id', 'from', 'to']));
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.complaints_pdf', compact('complaints'));
            return $pdf->download('complaints_export_' . now()->format('Y_m_d_H_i_s') . '.pdf' );

        } catch (\Exception $e) {
            return Response::Error($e->getMessage(), 403);
        }
    }
}
