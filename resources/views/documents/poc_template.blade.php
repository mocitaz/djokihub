<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $documentTitle }} - {{ $project->project_name }}</title>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm;
            size: A4;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #2d3748;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 210mm;
        }

        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #0891b2;
            padding-bottom: 10px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .company-logo {
            max-width: 120px;
            max-height: 40px;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #0891b2;
            margin: 0 0 4px 0;
            line-height: 1.1;
        }

        .company-tagline {
            font-size: 9pt;
            color: #718096;
            margin: 0 0 6px 0;
            font-style: italic;
        }
        
        .company-contact {
            font-size: 8pt;
            color: #4a5568;
            line-height: 1.2;
        }
        
        .company-contact div {
            margin-bottom: 1px;
        }

        .document-title {
            font-size: 18pt;
            font-weight: bold;
            color: #2d3748;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .document-meta {
            background: #f0f9ff;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #0891b2;
        }

        .document-meta table {
            width: 100%;
            font-size: 8pt;
        }

        .document-meta td {
            padding: 1px 0;
            vertical-align: top;
        }

        .document-meta .label {
            font-weight: bold;
            color: #2d3748;
            width: 40%;
        }

        .document-meta .value {
            color: #4a5568;
        }

        /* Project Information */
        .project-info {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            color: white;
            padding: 12px;
            margin: 15px 0;
            border-radius: 6px;
            text-align: center;
        }
        
        .project-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .project-subtitle {
            font-size: 8pt;
            margin: 2px 0 0 0;
            opacity: 0.9;
        }
        
        /* Details Table */
        .details-wrapper {
            margin: 15px 0;
        }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0891b2;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .details-table th {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .details-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
            vertical-align: top;
            background: white;
        }
        
        .details-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }
        
        /* Content Sections */
        .content-section {
            margin: 15px 0;
        }

        .content-box {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            background: #f0f9ff;
            border-radius: 4px;
            font-size: 9pt;
            line-height: 1.4;
            border-left: 3px solid #0891b2;
        }
        
        .content-box p {
            margin: 0 0 8px 0;
        }
        
        .content-box p:last-child {
            margin-bottom: 0;
        }

        /* Requirements Table */
        .requirements-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .requirements-table th {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .requirements-table th.text-center {
            text-align: center;
        }
        
        .requirements-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            background: white;
        }

        .requirements-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .requirements-table .text-center {
            text-align: center;
        }
        
        .requirements-table .col-no {
            width: 8%;
        }
        
        /* Terms & Conditions */
        .terms-section {
            margin: 15px 0;
            padding: 10px 12px;
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            border-radius: 4px;
        }
        
        .terms-section .section-title {
            color: #f59e0b;
            border-bottom-color: #fef3c7;
        }
        
        .terms-section p {
            font-size: 8pt;
            color: #4a5568;
            line-height: 1.3;
            margin: 0 0 6px 0;
        }
        
        .terms-section ol,
        .terms-section ul {
            font-size: 8pt;
            color: #4a5568;
            line-height: 1.3;
            padding-left: 16px;
            margin: 6px 0;
        }
        
        .terms-section li {
            margin-bottom: 3px;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 20px;
            width: 100%;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }
        
        .signature-cell {
            width: 45%;
            text-align: center;
            padding: 12px;
            border: 2px solid #0891b2;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 8px;
            vertical-align: top;
        }
        
        .signature-label {
            font-size: 10pt;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
        }
        
        .signature-area {
            height: 60px;
            margin: 8px 0;
            position: relative;
        }
        
        .stamp-placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        
        .signature-line {
            border-top: 1px solid #4a5568;
            padding-top: 4px;
            width: 70%;
            margin: 0 auto;
        }
        
        .signer-name {
            font-weight: bold;
            font-size: 9pt;
            color: #2d3748;
            margin: 4px 0 2px 0;
        }
        
        .signer-title {
            font-size: 8pt;
            color: #6b7280;
            margin: 0;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .footer-text {
            font-size: 8pt;
            color: #718096;
            margin: 2px 0;
        }
        
        .footer-highlight {
            font-weight: bold;
            color: #0891b2;
        }

        /* Utilities */
        .clear {
            clear: both;
            height: 0;
            line-height: 0;
            font-size: 0;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-left">
                @if(isset($company['logo_path']) && $company['logo_path'] && file_exists(public_path($company['logo_path'])))
                    <img src="{{ public_path($company['logo_path']) }}" alt="{{ $company['name'] ?? 'Company Logo' }}" class="company-logo">
                @else
                    <h1 class="company-name">{{ $company['name'] ?? 'Djoki Coding' }}</h1>
                @endif
                
                <div class="company-tagline">Professional Development Services</div>
                
                <div class="company-contact">
                    @if(isset($company['phone']) && $company['phone'])
                    <div><strong>Phone:</strong> {{ $company['phone'] }}</div>
                    @endif
                    @if(isset($company['email']) && $company['email'])
                    <div><strong>Email:</strong> {{ $company['email'] }}</div>
                    @endif
                    @if(isset($company['website']) && $company['website'])
                    <div><strong>Website:</strong> {{ $company['website'] }}</div>
                    @endif
                    @if(isset($company['address']) && $company['address'])
                    <div><strong>Address:</strong> {{ $company['address'] }}</div>
                    @endif
                </div>
            </div>
            
            <div class="header-right">
                <h1 class="document-title">{{ $documentTitle }}</h1>
                
                <div class="document-meta">
                <table>
                    <tr>
                            <td class="label">Document No.:</td>
                        <td class="value">{{ $project->order_id ?: ('PROJECT-' . $project->id) }}</td>
                    </tr>
                        <tr>
                            <td class="label">Issue Date:</td>
                            <td class="value">{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</td>
                        </tr>
                </table>
                </div>
            </div>
        </div>
        
        <!-- Project Information -->
        <div class="project-info">
            <h2 class="project-title">{{ $project->project_name }}</h2>
            <p class="project-subtitle">Proof of Concept Documentation</p>
        </div>

        <!-- Project Details -->
        <div class="details-wrapper">
            <h3 class="section-title">Project Details</h3>

            <table class="details-table">
                <thead>
                <tr>
                        <th style="width: 20%;">Client Name</th>
                        <th style="width: 30%;">{{ $project->client_name ?? 'N/A' }}</th>
                        <th style="width: 20%;">Project Budget</th>
                        <th style="width: 30%;">
                        Rp {{ number_format(floatval($project->budget ?? 0), 0, ',', '.') }}
                        @if($project->payment_status == 'Fully Paid')
                                <br><span style="color: #16a34a; font-weight:bold; font-size: 8pt;">✓ FULLY PAID</span>
                                <br><span style="color: #16a34a; font-size: 7pt;">Payment completed in full</span>
                        @elseif(in_array($project->payment_status, ['DP Paid', 'DP']))
                                <br><span style="color: #f59e0b; font-weight:bold; font-size: 8pt;">⚠ PARTIAL PAYMENT</span>
                                <br><span style="color: #f59e0b; font-size: 7pt;">Down payment received</span>
                        @elseif($project->payment_status == 'Unpaid')
                                <br><span style="color: #dc2626; font-weight:bold; font-size: 8pt;">✗ UNPAID</span>
                                <br><span style="color: #dc2626; font-size: 7pt;">No payment received yet</span>
                            @else
                                <br><span style="color: #6b7280; font-weight:bold; font-size: 8pt;">{{ strtoupper($project->payment_status ?? 'PENDING') }}</span>
                                <br><span style="color: #6b7280; font-size: 7pt;">Payment status unclear</span>
                        @endif
                        </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                        <td class="font-bold">Start Date:</td>
                    <td>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->isoFormat('D MMMM YYYY') : 'N/A' }}</td>
                        <td class="font-bold">Target Completion:</td>
                    <td>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->isoFormat('D MMMM YYYY') : 'N/A' }}</td>
                </tr>
                <tr>
                        <td class="font-bold">Project Status:</td>
                    <td>{{ $project->status ?? 'N/A' }}</td>
                        <td class="font-bold">Payment Status:</td>
                    <td>{{ $project->payment_status ?? 'N/A' }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Project Overview -->
        <div class="content-section">
            <h3 class="section-title">Project Objectives & Overview</h3>
            <div class="content-box">
                <p>{!! nl2br(e($project->description ?? 'No project description available for this Proof of Concept.')) !!}</p>
            </div>
        </div>

        <!-- Requirements & Scope -->
        <div class="content-section">
            <h3 class="section-title">Project Scope & Requirements</h3>
            
            <table class="requirements-table">
                <thead>
                    <tr>
                        <th class="col-no text-center">No.</th>
                        <th>Requirements & Project Scope</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requirements as $index => $req)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{!! nl2br(e($req->description)) !!}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center" style="padding: 15px; font-style: italic; color: #6b7280;">
                            No requirements or project scope recorded for this PoC.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Additional Notes -->
        @if(isset($project->notes) && !empty(trim($project->notes)))
        <div class="content-section">
            <h3 class="section-title">Additional Notes</h3>
            <div class="content-box">
                <p>{!! nl2br(e($project->notes)) !!}</p>
            </div>
        </div>
        @endif

        <!-- Terms & Conditions -->
        <div class="terms-section">
            <h3 class="section-title">Terms & Conditions (PoC)</h3>
            <p>This Proof of Concept (PoC) document aims to define the initial scope of work, cost estimates, and project timeline. Details in this document may change based on further discussion and requirement adjustments.</p>
            <ul>
                <li>Cost estimates and timeline are tentative and may be adjusted after deeper requirement analysis.</li>
                <li>Changes to the scope of work beyond what has been agreed in this PoC will require re-discussion and possible adjustment of costs and timeline.</li>
                <li>Down Payment (DP) of the agreed percentage is required before work begins (if applicable).</li>
                <li>This PoC is valid for 14 days from the date of issue unless otherwise agreed.</li>
                <li>Final project specifications and deliverables will be detailed in a separate project agreement upon PoC approval.</li>
            </ul>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <p class="signature-label">Second Party (Client)</p>
                        <div class="signature-area">
                            <!-- Space for client signature -->
                        </div>
                        <div class="signature-line">
                            <p class="signer-name">{{ $project->client_name }}</p>
                            <p class="signer-title">Client</p>
                        </div>
                    </td>
                    <td style="width: 10%;"></td>
                    <td class="signature-cell">
                        <p class="signature-label">First Party (Service Provider)</p>
                        <div class="signature-area">
                            <div class="stamp-placeholder">
                                <svg width="100" height="50" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1" y="1" width="98" height="48" rx="4" ry="4" style="fill:#e0f2fe; stroke:#0891b2; stroke-width:1;"/>
                                    <rect x="4" y="4" width="92" height="42" rx="2" ry="2" style="fill:none; stroke:#0891b2; stroke-width:0.5; stroke-dasharray: 2,1.5;"/>
                                    <text x="50" y="22" font-family="'DejaVu Sans', sans-serif" font-size="10" fill="#0e7490" text-anchor="middle" font-weight="bold">DJOKI CODING</text>
                                    <line x1="20" y1="27" x2="80" y2="27" style="stroke:#0891b2; stroke-width:0.5"/>
                                    <text x="50" y="37" font-family="'DejaVu Sans', sans-serif" font-size="7" fill="#0e7490" text-anchor="middle">VERIFIED</text>
                                    <text x="50" y="45" font-family="'DejaVu Sans', sans-serif" font-size="7" fill="#0e7490" text-anchor="middle">&amp; AUTHORIZED</text>
                                </svg>
                            </div>
                        </div>
                        <div class="signature-line">
                            <p class="signer-name">{{ $company['representative_name'] ?? ($company['name'] ?? 'Company Representative') }}</p>
                        <p class="signer-title">{{ $company['representative_title'] ?? 'Project Manager' }}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                <span class="footer-highlight">Thank you for considering our proposal!</span><br>
                This document serves as a comprehensive overview of the proposed project concept and approach.
            </p>
            <p class="footer-text">
                This is an official Proof of Concept document. Please retain this for your records.
            </p>
        </div>
    </div>
</body>
</html>
