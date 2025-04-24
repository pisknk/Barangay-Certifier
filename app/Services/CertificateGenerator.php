<?php

namespace App\Services;

require_once base_path('vendor/setasign/fpdf/fpdf.php');

use FPDF;

class CertificateGenerator extends FPDF
{
    protected $certificateType;
    protected $data;
    protected $tenantSettings;
    
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->SetAutoPageBreak(true, 10);
        
        // Get the current tenant settings
        $this->loadTenantSettings();
    }
    
    /**
     * Load tenant settings
     */
    protected function loadTenantSettings()
    {
        // Get authenticated tenant user
        $user = auth()->guard('tenant')->user();
        
        if ($user) {
            // Get the settings for the current user
            $this->tenantSettings = \App\Models\Tenant\TenantSetting::firstWhere('tenant_user_id', $user->id);
        }
        
        // If no settings found, create a default object
        if (!$this->tenantSettings) {
            $this->tenantSettings = new \stdClass();
            $this->tenantSettings->barangay_logo = '/assets/img/default-barangay-logo.png';
            $this->tenantSettings->municipality_logo = '/assets/img/default-municipality-logo.png';
            $this->tenantSettings->province = '';
            $this->tenantSettings->municipality = '';
            $this->tenantSettings->municipality_type = 'Municipality';
        }
    }
    
    /**
     * Set certificate data
     *
     * @param string $type
     * @param array $data
     * @return $this
     */
    public function setCertificateData($type, $data)
    {
        $this->certificateType = $type;
        $this->data = $data;
        return $this;
    }
    
    /**
     * Generate certificate PDF
     *
     * @return string The filename of the generated PDF
     */
    public function generate()
    {
        $this->AddPage();
        
        // Common header for all certificates
        $this->drawHeader();
        
        // Draw specific certificate content based on type
        switch ($this->certificateType) {
            case 'barangay_clearance':
                $this->drawBarangayClearance();
                break;
            case 'indigency':
                $this->drawIndigencyCertificate();
                break;
            case 'residency':
                $this->drawResidencyCertificate();
                break;
            case 'business_clearance':
                $this->drawBusinessClearance();
                break;
        }
        
        $filename = 'certificate_' . time() . '.pdf';
        $directory = storage_path('app/public/certificates');
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filepath = $directory . '/' . $filename;
        $this->Output('F', $filepath);
        
        return $filename;
    }
    
    /**
     * Draw certificate header
     */
    protected function drawHeader()
    {
        // Get tenant information
        $tenant = tenant();
        $barangayName = $tenant->barangay ?? 'Unknown Barangay';
        
        // Set top margin
        $this->SetY(10);
        
        // Try to add barangay logo on the left
        if (!empty($this->tenantSettings->barangay_logo)) {
            $logoPath = $this->tenantSettings->barangay_logo;
            
            // For URLs that start with http or https
            if (strpos($logoPath, 'http') === 0) {
                try {
                    $this->Image($logoPath, 15, 10, 30, 30, '', '');
                } catch (\Exception $e) {
                    // If image loading fails, no fallback text needed as we'll keep only the larger barangay text
                }
            } else {
                // For local files
                $realPath = public_path($logoPath);
                if (file_exists($realPath)) {
                    try {
                        $this->Image($realPath, 15, 10, 30, 30, '', '');
                    } catch (\Exception $e) {
                        // If image loading fails, no fallback text needed
                    }
                }
            }
        }
        
        // Try to add municipality logo on the right
        if (!empty($this->tenantSettings->municipality_logo)) {
            $logoPath = $this->tenantSettings->municipality_logo;
            
            // For URLs that start with http or https
            if (strpos($logoPath, 'http') === 0) {
                try {
                    $this->Image($logoPath, 165, 10, 30, 30, '', '');
                } catch (\Exception $e) {
                    // If image loading fails, no fallback text needed
                }
            } else {
                // For local files
                $realPath = public_path($logoPath);
                if (file_exists($realPath)) {
                    try {
                        $this->Image($realPath, 165, 10, 30, 30, '', '');
                    } catch (\Exception $e) {
                        // If image loading fails, no fallback text needed
                    }
                }
            }
        }
        
        // Position for header text - centered and aligned with logos
        $this->SetY(15);
        
        // Header text
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, 'Republic of the Philippines', 0, 1, 'C');
        
        // Province
        if (!empty($this->tenantSettings->province)) {
            $this->Cell(0, 6, 'Province of ' . $this->tenantSettings->province, 0, 1, 'C');
        }
        
        // Municipality
        if (!empty($this->tenantSettings->municipality)) {
            $this->Cell(0, 6, $this->tenantSettings->municipality_type . ' of ' . $this->tenantSettings->municipality, 0, 1, 'C');
        }
        
        $this->Ln(8);
        
        // Larger barangay name
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'BARANGAY ' . strtoupper($barangayName), 0, 1, 'C');
        
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Office Of The Punong Barangay', 0, 1, 'C');
        
        // Certificate title
        switch ($this->certificateType) {
            case 'barangay_clearance':
                $title = 'BARANGAY CLEARANCE';
                break;
            case 'indigency':
                $title = 'CERTIFICATE OF INDIGENCY';
                break;
            case 'residency':
                $title = 'CERTIFICATE OF RESIDENCY';
                break;
            case 'business_clearance':
                $title = 'BUSINESS CLEARANCE';
                break;
            default:
                $title = 'CERTIFICATE';
        }
        
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $title, 0, 1, 'C');
        
        $this->SetLineWidth(0.5);
        $this->Line(15, 90, 195, 90);
        $this->SetLineWidth(0.2); // Reset line width to default
        
        $this->Ln(10);
    }
    
    /**
     * Draw Barangay Clearance content
     */
    protected function drawBarangayClearance()
    {
        // Set starting position
        $this->SetY(115);
        
        // To whom it may concern
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'To whom it may concern:', 0, 1, 'L');
        
        $this->Ln(5);
        
        // Certificate body with dynamic location data
        $this->SetFont('Arial', '', 12);
        
        // Get tenant information
        $tenant = tenant();
        $barangayName = $tenant->barangay ?? 'Unknown Barangay';
        $municipality = $this->tenantSettings->municipality ?? 'Unknown Municipality';
        $province = $this->tenantSettings->province ?? 'Unknown Province';
        
        $bodyText = "This is to certify that <b>{$this->data['full_name']}</b>, {$this->data['age']} years of age, born on " .
            "<b>" . (isset($this->data['birthdate']) ? $this->data['birthdate'] : 'N/A') . "</b> a native of <b>" . 
            strtoupper($municipality) . "</b> and presently residing at {$this->data['address']}, " .
            "Barangay {$barangayName}, {$municipality}, {$province} " . 
            (isset($this->data['since_year']) ? $this->data['since_year'] : "since year of N/A") . ".";
        
        $this->WriteHTML($bodyText);
        
        $this->Ln(10);
        
        // Purpose
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "Purpose: " . (isset($this->data['purpose']) ? $this->data['purpose'] : 'FOR APPLICATION OF PROBATION'), 0, 1, 'L');
        
        $this->Ln(5);
        
        // This certification is issued upon request statement
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "This certification is issued upon the request of {$this->data['full_name']} for any legal", 0, 1, 'L');
        $this->Cell(0, 10, "purpose it may serve.", 0, 1, 'L');
        
        $this->Ln(5);
        
        // Remarks
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(255, 0, 0);
        $this->Cell(0, 10, "Remarks: NO RECORDS OF ANY OFFENSE AND THAT THE SAID PERSON IS OF GOOD MORAL", 0, 1, 'L');
        $this->Cell(0, 10, "CHARACTER AND A LAW ABIDING CITIZEN OF THE COMMUNITY", 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);
        
        $this->Ln(5);
        
        // Date
        $currentDate = date('jS') . ' day of ' . strtoupper(date('F')) . ', ' . date('Y');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Done this {$currentDate} at Barangay {$barangayName}, {$municipality}, {$province}", 0, 1, 'L');
        $this->Cell(0, 10, "Philippines", 0, 1, 'L');
        
        $this->Ln(20);
        
        // Signatory
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->tenantSettings->punong_barangay ?? "HON. HALIM T. DIMACANGAN", 0, 1, 'R');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Punong Barangay", 0, 1, 'R');
    }
    
    /**
     * Draw Indigency Certificate content
     */
    protected function drawIndigencyCertificate()
    {
        // Set starting position
        $this->SetY(115);
        
        // To whom it may concern
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'To whom it may concern:', 0, 1, 'L');
        
        $this->Ln(5);
        
        // Get tenant information
        $tenant = tenant();
        $barangayName = $tenant->barangay ?? 'Unknown Barangay';
        $municipality = $this->tenantSettings->municipality ?? 'Unknown Municipality';
        $province = $this->tenantSettings->province ?? 'Unknown Province';
        
        // Certificate body
        $this->SetFont('Arial', '', 12);
        $this->WriteHTML(
            "This is to certify that <b>{$this->data['full_name']}</b>, {$this->data['age']} years of age, " .
            "<b>{$this->data['civil_status']}</b>, and presently residing at {$this->data['address']}, " .
            "Barangay {$barangayName}, {$municipality}, {$province}, Philippines is a bonafide resident of this barangay and " .
            "belongs to the indigent family in our barangay."
        );
        
        $this->Ln(10);
        
        // Purpose
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "Purpose: " . (isset($this->data['purpose']) ? $this->data['purpose'] : 'FOR APPLICATION OF PROBATION'), 0, 1, 'L');
        
        $this->Ln(5);
        
        // This certification is issued upon request statement
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "This certification is issued upon the request of {$this->data['full_name']} for any legal", 0, 1, 'L');
        $this->Cell(0, 10, "purpose it may serve.", 0, 1, 'L');
        
        $this->Ln(5);
        
        // Date
        $currentDate = date('jS') . ' day of ' . strtoupper(date('F')) . ', ' . date('Y');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Done this {$currentDate} at Barangay {$barangayName}, {$municipality}, {$province}", 0, 1, 'L');
        $this->Cell(0, 10, "Philippines", 0, 1, 'L');
        
        $this->Ln(20);
        
        // Signatory
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->tenantSettings->punong_barangay ?? "HON. HALIM T. DIMACANGAN", 0, 1, 'R');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Punong Barangay", 0, 1, 'R');
    }
    
    /**
     * Draw Residency Certificate content
     */
    protected function drawResidencyCertificate()
    {
        // Set starting position
        $this->SetY(115);
        
        // To whom it may concern
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'To whom it may concern:', 0, 1, 'L');
        
        $this->Ln(5);
        
        // Get tenant information
        $tenant = tenant();
        $barangayName = $tenant->barangay ?? 'Unknown Barangay';
        $municipality = $this->tenantSettings->municipality ?? 'Unknown Municipality';
        $province = $this->tenantSettings->province ?? 'Unknown Province';
        
        // Certificate body
        $this->SetFont('Arial', '', 12);
        $this->WriteHTML(
            "This is to certify that <b>{$this->data['full_name']}</b>, {$this->data['age']} years of age, " .
            "<b>{$this->data['civil_status']}</b>, and presently residing at {$this->data['address']}, " .
            "Barangay {$barangayName}, {$municipality}, {$province}, Philippines is a bonafide resident of this barangay " .
            (isset($this->data['since_year']) ? "since {$this->data['since_year']}" : "for several years") . "."
        );
        
        $this->Ln(10);
        
        // Purpose
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "Purpose: " . (isset($this->data['purpose']) ? $this->data['purpose'] : 'FOR APPLICATION OF PROBATION'), 0, 1, 'L');
        
        $this->Ln(5);
        
        // This certification is issued upon request statement
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "This certification is issued upon the request of {$this->data['full_name']} for any legal", 0, 1, 'L');
        $this->Cell(0, 10, "purpose it may serve.", 0, 1, 'L');
        
        $this->Ln(5);
        
        // Date
        $currentDate = date('jS') . ' day of ' . strtoupper(date('F')) . ', ' . date('Y');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Done this {$currentDate} at Barangay {$barangayName}, {$municipality}, {$province}", 0, 1, 'L');
        $this->Cell(0, 10, "Philippines", 0, 1, 'L');
        
        $this->Ln(20);
        
        // Signatory
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->tenantSettings->punong_barangay ?? "HON. HALIM T. DIMACANGAN", 0, 1, 'R');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Punong Barangay", 0, 1, 'R');
    }
    
    /**
     * Draw Business Clearance content
     */
    protected function drawBusinessClearance()
    {
        // Set starting position
        $this->SetY(115);
        
        // To whom it may concern
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'To whom it may concern:', 0, 1, 'L');
        
        $this->Ln(5);
        
        // Get tenant information
        $tenant = tenant();
        $barangayName = $tenant->barangay ?? 'Unknown Barangay';
        $municipality = $this->tenantSettings->municipality ?? 'Unknown Municipality';
        $province = $this->tenantSettings->province ?? 'Unknown Province';
        
        // Certificate body
        $this->SetFont('Arial', '', 12);
        $this->WriteHTML(
            "This is to certify that the business named <b>{$this->data['business_name']}</b>, owned by " .
            "<b>{$this->data['full_name']}</b>, with business address at {$this->data['business_address']}, " .
            "Barangay {$barangayName}, {$municipality}, {$province}, Philippines, nature of business <b>{$this->data['business_nature']}</b>, " .
            "has been granted BARANGAY BUSINESS CLEARANCE to operate within the territorial jurisdiction of this Barangay."
        );
        
        $this->Ln(10);
        
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "The business owner/operator shall comply with the existing Barangay Ordinances,", 0, 1, 'L');
        $this->Cell(0, 10, "Municipal Ordinances and existing laws.", 0, 1, 'L');
        
        $this->Ln(5);
        
        // Date
        $currentDate = date('jS') . ' day of ' . strtoupper(date('F')) . ', ' . date('Y');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Done this {$currentDate} at Barangay {$barangayName}, {$municipality}, {$province}", 0, 1, 'L');
        $this->Cell(0, 10, "Philippines", 0, 1, 'L');
        
        $this->Ln(20);
        
        // Signatory
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->tenantSettings->punong_barangay ?? "HON. HALIM T. DIMACANGAN", 0, 1, 'R');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, "Punong Barangay", 0, 1, 'R');
    }
    
    /**
     * Write HTML to PDF
     * Simple HTML support for basic formatting
     * 
     * @param string $html
     */
    protected function WriteHTML($html)
    {
        // HTML parser
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                // Text
                if ($e != '') {
                    $this->Write(6, $e);
                }
            } else {
                // Tag
                if ($e[0] == '/') {
                    $this->SetFont('', '');
                } elseif ($e[0] == 'b') {
                    $this->SetFont('', 'B');
                } elseif ($e[0] == 'i') {
                    $this->SetFont('', 'I');
                } elseif ($e[0] == 'u') {
                    $this->SetFont('', 'U');
                } elseif ($e == 'br') {
                    $this->Ln(5);
                }
            }
        }
        
        $this->Ln(6);
    }
    
    /**
     * Override certificate body text to use tenant settings
     * 
     * @param string $text The text to process
     * @return string
     */
    protected function processCertificateText($text)
    {
        // Get tenant information
        $tenant = tenant();
        $barangayName = $tenant->barangay ?? 'Unknown Barangay';
        $municipality = $this->tenantSettings->municipality ?? 'Unknown Municipality';
        $province = $this->tenantSettings->province ?? 'Unknown Province';
        
        // Replace hardcoded values with dynamic values
        $text = str_replace('Barangay Sandor', 'Barangay ' . $barangayName, $text);
        $text = str_replace('Balo-i', $municipality, $text);
        $text = str_replace('Lanao del Norte', $province, $text);
        
        return $text;
    }
} 