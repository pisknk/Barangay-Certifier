<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\CertificateGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantCertificateController extends Controller
{
    /**
     * Display the certificates selection page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('tenant.certificates.index');
    }
    
    /**
     * Show form for a specific certificate type
     * 
     * @param string $type
     * @return \Illuminate\View\View
     */
    public function showForm($type)
    {
        // Validate certificate type
        if (!in_array($type, ['barangay_clearance', 'indigency', 'residency', 'business_clearance'])) {
            return redirect()->route('tenant.certificates.index')->with('error', 'Invalid certificate type');
        }
        
        return view('tenant.certificates.form', ['type' => $type]);
    }
    
    /**
     * Process certificate form submission
     * 
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitForm(Request $request, $type)
    {
        // Validate certificate type
        if (!in_array($type, ['barangay_clearance', 'indigency', 'residency', 'business_clearance'])) {
            return redirect()->route('tenant.certificates.index')->with('error', 'Invalid certificate type');
        }
        
        // Basic validation
        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'age' => 'required|numeric|min:1|max:120',
            'civil_status' => 'required|string|max:50',
            'contact_number' => 'required|string|max:20',
            'terms' => 'required',
        ]);
        
        // Additional validation for specific certificate types
        if ($type == 'business_clearance') {
            $request->validate([
                'business_name' => 'required|string|max:255',
                'business_address' => 'required|string|max:255',
                'business_nature' => 'required|string|max:255',
            ]);
        }
        
        if ($type == 'indigency') {
            $request->validate([
                'purpose' => 'required|string|max:255',
            ]);
        }
        
        // Additional fields for barangay clearance
        if ($type == 'barangay_clearance') {
            $request->validate([
                'birthdate' => 'nullable|date',
                'since_year' => 'nullable|string|max:4',
                'purpose' => 'nullable|string|max:255',
            ]);
        }
        
        // Additional fields for residency certificate
        if ($type == 'residency') {
            $request->validate([
                'purpose' => 'nullable|string|max:255',
            ]);
        }
        
        // Generate PDF certificate
        $certificateData = $request->all();
        $generator = new CertificateGenerator();
        $filename = $generator->setCertificateData($type, $certificateData)->generate();
        
        $certificateName = '';
        switch ($type) {
            case 'barangay_clearance':
                $certificateName = 'Barangay Clearance';
                break;
            case 'indigency':
                $certificateName = 'Certificate of Indigency';
                break;
            case 'residency':
                $certificateName = 'Certificate of Residency';
                break;
            case 'business_clearance':
                $certificateName = 'Business Clearance';
                break;
        }
        
        // Store certificate data in the database here if needed
        
        // Redirect to success page with the filename
        return view('tenant.certificates.success', compact('filename'));
    }
    
    /**
     * Download the generated certificate
     * 
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCertificate($filename)
    {
        $path = storage_path('app/public/certificates/' . $filename);
        
        if (!file_exists($path)) {
            return redirect()->route('tenant.certificates.index')
                ->with('error', 'Certificate not found.');
        }
        
        return response()->download($path, $filename);
    }
    
    /**
     * View the generated certificate
     * 
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function viewCertificate($filename)
    {
        $path = storage_path('app/public/certificates/' . $filename);
        
        if (!file_exists($path)) {
            return redirect()->route('tenant.certificates.index')
                ->with('error', 'Certificate not found.');
        }
        
        return response()->file($path);
    }
    
    /**
     * Email the certificate to a resident
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $filename
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailCertificate(Request $request, $filename)
    {
        try {
            // Validate request
            $request->validate([
                'resident_email' => 'required|email',
                'email_message' => 'nullable|string',
            ]);
            
            $path = storage_path('app/public/certificates/' . $filename);
            
            if (!file_exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate not found.'
                ], 404);
            }
            
            // Get tenant information for the email
            $tenant = tenant();
            $barangayName = $tenant->barangay ?? 'Barangay Office';
            
            // Send the email with attachment
            \Mail::send('emails.certificate', [
                'emailMessage' => $request->email_message,
                'barangayName' => $barangayName,
            ], function ($message) use ($request, $path, $filename, $barangayName) {
                $message->to($request->resident_email)
                    ->subject('Your Certificate from ' . $barangayName)
                    ->attach($path, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
            });
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Certificate has been sent to ' . $request->resident_email
            ]);
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to send certificate email: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
} 