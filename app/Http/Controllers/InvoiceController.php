<?php

namespace App\Http\Controllers;

use App\Models\Project; // Pastikan path ke model Project Anda benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Untuk PDF facade (DomPDF)
use Carbon\Carbon; // Untuk manipulasi dan format tanggal
use Illuminate\Support\Facades\Log; // Untuk logging jika terjadi error
use Illuminate\Support\Str; // Untuk helper string seperti Str::slug

class InvoiceController extends Controller
{
    public function index()
    {
        $projects = Project::whereNotNull('budget')
                           ->where('budget', '>', 0)
                           ->whereNotNull('client_name')
                           ->orderBy('updated_at', 'desc')
                           ->paginate(15);

        return view('invoices.index', compact('projects'));
    }

    public function generatePdf(Project $project)
    {
        Log::info("Attempting to generate Payment Receipt for Project ID: {$project->id}");

        if (empty($project->budget) || !is_numeric($project->budget) || $project->budget < 0) {
            Log::error("Invalid or missing budget for Project ID: {$project->id}. Budget: '{$project->budget}'");
            return redirect()->route('invoices.index')->with('error', 'Harga Proyek tidak valid atau kosong untuk membuat bukti pembayaran.');
        }
        if (empty($project->client_name)) {
            Log::error("Missing client name for Project ID: {$project->id}");
            return redirect()->route('invoices.index')->with('error', 'Nama Klien kosong untuk membuat bukti pembayaran.');
        }

        // Path logo, pastikan logo ada di public/images/nama_logo_anda.png
        $logoNameToTry = 'logo_djokicoding.png'; // Nama file logo yang diinginkan
        $defaultLogoName = 'logo_djokihub.png';   // Nama file logo fallback
        $logoPathInPublic = public_path('images/' . $logoNameToTry);
        if (!file_exists($logoPathInPublic)) {
            $logoPathInPublic = public_path('images/' . $defaultLogoName);
        }
        if (!file_exists($logoPathInPublic)) {
            $logoPathInPublic = null; // Tidak ada logo jika keduanya tidak ditemukan
        }


        $companyDetails = [
            'name' => config('app.company_name', 'Djoki Coding'),
            'phone' => config('app.company_phone', '+62 851-7442-4245'),
            'email' => config('app.company_email', 'djokicoding@gmail.com'),
            'instagram' => config('app.company_instagram', '@djokicoding'),
            'tax_id' => config('app.company_tax_id', null),
            'logo_path' => $logoPathInPublic,
        ];
        Log::debug("Company Details for PDF:", $companyDetails);

        $referenceNumber = 'RCPT/' . Carbon::now()->format('Ymd') . '/' . str_pad($project->id, 4, '0', STR_PAD_LEFT) . ($project->order_id ? ('/' . Str::limit(str_replace(['/', '#'], '-', $project->order_id), 10,'')) : '');
        $paymentDate = Carbon::now(); 

        $items = [
            [
                'description' => 'Pembayaran untuk Proyek: ' . $project->project_name, 
                'details' => $project->description ? Str::limit($project->description, 150) : 'Pelunasan biaya proyek.',
                'total_price' => floatval($project->budget)
            ],
        ];

        $subtotal = floatval($project->budget);
        $discountAmount = 0; 
        $taxAmount = 0;
        $grandTotal = $subtotal - $discountAmount + $taxAmount; 

        $thankYouMessage = "Kami mengucapkan terima kasih atas kepercayaan dan pembayaran tepat waktu yang telah Anda berikan. Kami berharap dapat terus melayani Anda dengan layanan terbaik kami. Jangan ragu untuk menghubungi kami jika ada pertanyaan lebih lanjut atau kebutuhan proyek di masa mendatang.";

        $data = [
            'companyName' => $companyDetails['name'],
            'companyPhone' => $companyDetails['phone'],
            'companyEmail' => $companyDetails['email'],
            'companyInstagram' => $companyDetails['instagram'],
            'companyTaxId' => $companyDetails['tax_id'],
            'companyLogoPath' => $companyDetails['logo_path'],

            'clientName' => $project->client_name,
            'clientAddress' => $project->client_address ?? 'Alamat tidak tersedia', 
            'clientEmail' => $project->client_email ?? null,
            'clientPhone' => $project->client_phone ?? null,

            'invoiceNumber' => $referenceNumber,
            'paymentDate' => $paymentDate->isoFormat('D MMMM YYYY'), 
            'paymentMethod' => 'Transfer Bank / QRIS', // <<< PERUBAHAN DI SINI
            'projectOrderId' => $project->order_id,
            'projectName' => $project->project_name,

            'items' => $items,
            'subtotal' => $subtotal,
            'discountAmount' => $discountAmount,
            'grandTotal' => $grandTotal,
            
            'notes' => $project->notes_for_receipt ?? $thankYouMessage,
        ];
        Log::debug("Data passed to PDF view for Project ID {$project->id}:", $data);

        try {
            $pdf = App::make('dompdf.wrapper');
            $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true, 'defaultFont' => 'Helvetica']);
            $pdf->loadView('invoices.pdf_template', $data); 
            
            $pdfFileName = 'ProofOfPayment-' . Str::slug($project->project_name) . '-' . str_replace('/', '_', $referenceNumber) . '.pdf';

            Log::info("Successfully loaded PDF view for Project ID: {$project->id}. Attempting to download as {$pdfFileName}");
            return $pdf->download($pdfFileName);

        } catch (\Exception $e) {
            Log::error("Error generating PDF for Project ID {$project->id}: " . $e->getMessage(), [
                'exception_trace' => $e->getTraceAsString(),
                'data_passed' => $data 
            ]);
            if (Str::contains($e->getMessage(), 'View [invoices.pdf_template] not found')) {
                 return redirect()->route('invoices.index')->with('error', 'Template PDF bukti pembayaran tidak ditemukan. Harap hubungi administrator.');
            }
            return redirect()->route('invoices.index')->with('error', 'Gagal membuat PDF bukti pembayaran: ' . $e->getMessage());
        }
    }
}