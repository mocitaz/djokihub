<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Proof of Payment - {{ $invoiceNumber }}</title>
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
            border-bottom: 2px solid #2b6cb0;
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
            color: #2b6cb0;
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
        
        .invoice-title {
            font-size: 22pt;
            font-weight: bold;
            color: #2d3748;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .invoice-meta {
            background: #f7fafc;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #2b6cb0;
        }
        
        .invoice-meta table {
            width: 100%;
            font-size: 8pt; 
        }
        
        .invoice-meta td {
            padding: 1px 0;
            vertical-align: top;
        }
        
        .invoice-meta .label {
            font-weight: bold;
            color: #2d3748;
            width: 35%;
        }

        .invoice-meta .value {
            color: #4a5568;
        }
        
        /* Billing Information */
        .billing-section {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        
        .billing-col {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .billing-col.right {
            width: 48%;
            padding-left: 4%;
        }
        
        .billing-header {
            font-size: 10pt; 
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 6px;
            padding: 4px 8px;
            background: #edf2f7;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .billing-content {
            padding: 0 8px;
            font-size: 9pt;
            line-height: 1.2;
        }
        
        .billing-content p {
            margin: 1px 0; 
            color: #4a5568;
        }

        .billing-content .primary-text {
            font-weight: bold;
            color: #2d3748;
            font-size: 10pt;
        }
        
        /* Project Information */
        .project-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            margin: 15px 0;
            border-radius: 6px;
            text-align: center;
        }
        
        .project-title {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .project-subtitle {
            font-size: 8pt;
            margin: 2px 0 0 0;
            opacity: 0.9;
        }
        
        /* Items Table */
        .items-wrapper {
            margin: 15px 0;
        }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%);
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
            vertical-align: top;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .items-table tbody tr:hover {
            background: #edf2f7;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .item-description {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 2px;
        }
        
        .item-details {
            font-size: 8pt;
            color: #718096;
            font-style: italic;
        }
        
        .amount {
            font-weight: bold;
            color: #2b6cb0;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 15px;
        }
        
        .summary-table {
            width: 250px;
            float: right;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 4px 10px;
            font-size: 9pt;
        }
        
        .summary-table .label {
            text-align: right;
            font-weight: 500;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .summary-table .amount {
            text-align: right;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
        }
        
        .summary-table .discount {
            color: #e53e3e;
        }
        
        .summary-table .total-row {
            background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%);
            color: white;
        }
        
        .summary-table .total-row td {
            padding: 8px 10px;
            font-size: 11pt;
            font-weight: bold;
            border: none;
        }
        
        /* Payment Status */
        .payment-status {
            clear: both;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            border-radius: 6px;
            text-align: center;
            color: white;
        }
        
        .status-badge {
            display: inline-block;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .status-date {
            font-size: 9pt;
            opacity: 0.9;
        }
        
        /* Notes Section */
        .notes-section {
            margin-top: 15px;
            padding: 8px;
            background: #f8fafc;
            border-left: 3px solid #4299e1;
            border-radius: 3px;
        }
        
        .notes-title {
            font-size: 10pt;
            font-weight: bold;
            color: #2d3748;
            margin: 0 0 4px 0;
        }
        
        .notes-content {
            font-size: 9pt;
            color: #4a5568;
            line-height: 1.2;
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
            color: #2b6cb0;
        }
        
        /* Utilities */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .mb-0 {
            margin-bottom: 0;
        }
        
        .mt-0 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-left">
                @if(isset($companyLogoPath) && $companyLogoPath && file_exists($companyLogoPath))
                    <img src="{{ $companyLogoPath }}" alt="{{ $companyName ?? 'Logo' }}" class="company-logo">
                @else
                    <h1 class="company-name">{{ $companyName ?? 'Djoki Coding' }}</h1>
                @endif
                
                <div class="company-tagline">Professional Development Services</div>
                
                <div class="company-contact">
                    @if(isset($companyPhone) && $companyPhone && trim($companyPhone) !== '')
                    <div><strong>Phone:</strong> {{ $companyPhone }}</div>
                    @endif
                    @if(isset($companyEmail) && $companyEmail && trim($companyEmail) !== '')
                    <div><strong>Email:</strong> {{ $companyEmail }}</div>
                    @endif
                    @if(isset($companyInstagram) && $companyInstagram && trim($companyInstagram) !== '')
                    <div><strong>Instagram:</strong> {{ $companyInstagram }}</div>
                    @endif
                    @if(isset($companyTaxId) && $companyTaxId && trim($companyTaxId) !== '')
                    <div><strong>NPWP:</strong> {{ $companyTaxId }}</div>
                @endif
                </div>
            </div>
            
            <div class="header-right">
                <h1 class="invoice-title">Proof of Payment</h1>
                
                <div class="invoice-meta">
                <table>
                    <tr>
                        <td class="label">Reference #:</td>
                        <td class="value">{{ $invoiceNumber }}</td>
                    </tr>
                    <tr>
                        <td class="label">Payment Date:</td>
                        <td class="value">{{ $paymentDate }}</td>
                    </tr>
                        @if(isset($projectOrderId) && $projectOrderId && trim($projectOrderId) !== '')
                    <tr>
                        <td class="label">Order ID:</td>
                        <td class="value">{{ $projectOrderId }}</td>
                    </tr>
                    @endif
                        @if(isset($paymentMethod) && $paymentMethod && trim($paymentMethod) !== '' && $paymentMethod !== 'N/A')
                    <tr>
                        <td class="label">Payment Method:</td>
                        <td class="value">{{ $paymentMethod }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-col">
                <div class="billing-header">Paid By</div>
                <div class="billing-content">
                    <p class="primary-text">{{ $clientName ?? 'Client Name' }}</p>
                    @if(isset($clientAddress) && $clientAddress && trim($clientAddress) !== '' && !in_array(strtolower(trim($clientAddress)), ['alamat tidak tersedia', 'tidak tersedia', 'n/a', 'address not available', 'not available']))
                    <p>{!! nl2br(e($clientAddress)) !!}</p>
                    @endif
                    @if(isset($clientEmail) && $clientEmail && trim($clientEmail) !== '')
                    <p><strong>Email:</strong> {{ $clientEmail }}</p>
                    @endif
                    @if(isset($clientPhone) && $clientPhone && trim($clientPhone) !== '')
                    <p><strong>Phone:</strong> {{ $clientPhone }}</p>
                    @endif
                </div>
            </div>
            
            <div class="billing-col right">
                <div class="billing-header">Received By</div>
                <div class="billing-content">
                    <p class="primary-text">{{ $companyName ?? 'Djoki Coding' }}</p>
                    @if(isset($companyEmail) && $companyEmail && trim($companyEmail) !== '')
                    <p><strong>Email:</strong> {{ $companyEmail }}</p>
                    @endif
                    @if(isset($companyPhone) && $companyPhone && trim($companyPhone) !== '')
                    <p><strong>Phone:</strong> {{ $companyPhone }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Project Information -->
        <div class="project-info">
            <h2 class="project-title">{{ $projectName }}</h2>
            <p class="project-subtitle">Professional Development Services</p>
        </div>
        
        <!-- Items Section -->
        <div class="items-wrapper">
            <h3 class="section-title">Payment Details</h3>

        <table class="items-table">
            <thead>
                <tr>
                        <th style="width: 70%;">Description</th>
                        <th class="text-right" style="width: 30%;">Amount Paid (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr>
                    <td>
                            <div class="item-description">{{ $item['description'] }}</div>
                        @if(isset($item['details']) && !empty($item['details']))
                            <div class="item-details">{{ $item['details'] }}</div>
                        @endif
                    </td>
                        <td class="text-right amount">
                            Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                        </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section clearfix">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @if (isset($discountAmount) && $discountAmount > 0)
                <tr>
                    <td class="label">Discount{{ isset($discountPercentage) ? ' ('.$discountPercentage.'%)' : '' }}:</td>
                    <td class="amount discount">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL PAID:</td>
                    <td class="amount">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Status -->
        <div class="payment-status">
            <div class="status-badge">
                @php
                    $statusText = 'Payment Received';
                    if (isset($paymentMethod) && strtoupper($paymentMethod) === 'LUNAS') {
                        $statusText = 'Paid in Full';
                    } elseif (isset($project) && property_exists($project, 'payment_status') && strtoupper($project->payment_status) === 'FULLY PAID') {
                        $statusText = 'Paid in Full';
                    }
                @endphp
                {{ $statusText }}
            </div>
            <div class="status-date">
                Payment received on {{ $paymentDate }}
            </div>
        </div>

        <!-- Notes Section -->
        @if (isset($notes) && !empty($notes))
        <div class="notes-section">
            <h4 class="notes-title">Additional Notes</h4>
            <div class="notes-content">
                {!! nl2br(e($notes)) !!}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                <span class="footer-highlight">Thank you for your payment!</span><br>
                We appreciate your prompt payment and look forward to continuing our partnership.
            </p>
            <p class="footer-text">
                This is an official proof of payment document. Please retain this for your records.
            </p>
        </div>
    </div>
</body>
</html>