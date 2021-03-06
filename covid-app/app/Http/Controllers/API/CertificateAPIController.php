<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

// Models
use App\Models\User;
use App\Models\Certificate;

// Facades
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Utilities
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

// Packages
// use Intervention\Image\ImageManagerStatic as Image;

class CertificateAPIController extends Controller
{
    /**
     * Authenticated accessible methods
     */
    public function providerCertificateStatus(User $provider) {
        $certificate = $provider->certificates()->orderBy('id', 'desc')->limit(1)->first();

        if (! $certificate)
            return response()->json(
                $this->error('Provider did not submitted a certificate yet!'),
            404);

        return $this->success('Received provider certificate status', [
            'approved' => $certificate->status === 'approved' ? true : false
        ]);
    }

    /**
     * Provider accessible methods
     */
    public function store(Request $request) {
        $user = auth('api')->user(); // Get the current logged in user

        $unRejectedCertificatesCount = (int) $user->certificates()->where('status', '!=', 'rejected')->count();
        // Make sure the provider does not already has a pending/approved certificate
        if ($unRejectedCertificatesCount > 0)
            return response()->json(
                $this->error('Either a certificate is already awaiting for approval or it is already approved!'),
            406);

        $attempts = (int) $user->certificates()->count();
        // Provider has reached the limit of maximum certificate uploads
        if ($attempts >= Certificate::MAX_UPLOAD_ATTEMPTS)
            return response()->json([
                $this->error('Max certificate creation attempts reached!')
            ], 406);

        // Make sure the file was uploaded successfully
        if (! $request->hasFile('certificate') || !$request->file('certificate')->isValid())
            return response()->json(
                $this->error('Did not received certificate document or the upload failed!'),
            400);

        
        $image = $request->file('certificate'); // Get the uploaded file
        $extension = $image->extension();

        if (! preg_match(Certificate::ALLOWED_FILE_EXT, $extension))
            return response()->json(
                $this->error('Only image are accepted as certificate document!'),
            400);


        $uuid = Str::orderedUuid(); // Generate a unique ID for certificate
        $filename = $uuid . "." . /* $image->extension() */ "jpg";

        // Try to save the uploaded file under `/storage/certificates` directory
        // and process it using the Intervention package
        try {
            // Save the image to specified path
            $path = $image->storeAs('certificates', $filename);
            $filePath = 'storage/certificates/' . $filename;
            $storePath = 'storage/certificates/thumbnails/' . $filename;

            // Make sure the `certificates/thumbnails` folder exists to prevent
            // errors caused by saving processed image in directory which does
            // not exists
            if (! collect(Storage::directories('certificates'))->contains('certificates/thumbnails'))
                Storage::makeDirectory('certificates/thumbnails');


            // Process the image
            $img = Image::make($filePath);
            $img->resize(300, 300);

            // Save the image to storage and free up memory
            $img->save($storePath);
            $img->destroy();
        } catch (Exception $e) {
            $message = $e->getMessage();
            $message = empty($message) || is_null($message) ? "Unknown error" : $message;
            
            // Log the message to logger
            Log::error("An error occurred while saving certificate file!", [
                'error' => $message,
                'filename' => $filename
            ]);
    
            // Return error JSON response
            return response()->json(
                $this->error('An error occurred while saving certificate!'),
            500);
        }

        // Try to create new record for certificate in the database
        try {
            // Create a new certificate for current logged in user
            $certificate = $user->certificates()->create([
                'ref' => $uuid,
                // By default the status will be set to `pending` at database level
            ]);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $message = empty($message) || is_null($message) ? "Unknown error" : $message;
            
            // Log the message to logger
            Log::error("An error occurred while saving certificate to database!", [
                'error' => $message,
                'user' => $user->id,
                'image' => $path
            ]);
    
            // Return error JSON response
            return response()->json(
                $this->error('An error occurred while saving certificate!'),
             500);
        }

        // Certificate was added successfully!
        return $this->success('Certificate was created successfully!', [
            'certificate' => [
                'ref' => $certificate->ref, // Unique certificate ID
                'status' => 'pending',
                'image' => url(Storage::url('certificates/' . $filename))
            ]
        ]);
    }

    public function show(Certificate $certificate) {
        $user = auth('api')->user();

        // Provider can only view his own certificate
        if ($user->id !== $certificate->user()->first()->id)
            return response()->json(
                $this->error("Cannot view someone else's certificate!"),
            403);

        $filename = $certificate->ref . ".jpg";

        // Respond with the certificate uid and image path
        return $this->success('Successfully retrieved certificate!', [
            'certificate' => [
                'ref' => $certificate->ref, // Unique certificate ID
                'status' => $certificate->status,
                'image' => url(Storage::url('certificates/' . $filename))
            ]
        ]);
    }

    public function myCertificate(Request $request) {
        $user = auth('api')->user();
        
        // Get user's latest certificate
        $certificate = $user->certificates()->orderBy('id', 'desc')->limit(1)->first();

        // Send a 404 response which means that user has not submitted a certificate yet
        if (! $certificate)
            return response()->json($this->error('No certificates found!'), 404);

        $filename = $certificate->ref . ".jpg";

        return $this->success('Successfully retrieved personal certificate', [
            'certificate' => [
                'ref' => $certificate->ref,
                'status' => $certificate->status,
                'image' => url(Storage::url('certificates/' . $filename))
            ]
        ]);
    }

    /**
     * Admin accessible methods
     */
    public function index(Request $request) {
        return $this->success('Successfully retrieved all certificates!', [
            // This method will spit out additional data like timestamps
            // and primary key but it's okay because this will be sent
            // to super admin only
            'certificates' => Certificate::all()
        ]);
    }

    public function update(Request $request, Certificate $certificate) {
        $status = $request->input('status', null);

        // Status must be present with one of two values (`approved` or `rejected`)
        if (! is_string($status) || ! preg_match('/^(approved|rejected)$/', $status))
            return response()->json($this->error('Invalid value provided for "status" field!'), 400);
        
        // Keep track of original status (will be used for logging errors)
        $old_status = $certificate->status;
        $filename = $certificate->ref . ".jpg"; // Certificate image

        // Try to update the certificate status
        try {
            $certificate->status = $status;
            $certificate->save();
        }
        // Return error response and log the event to logger
        catch (Exception $e) {
            $message = $e->getMessage();
            $message = empty($message) || is_null($message) ? "Unknown error" : $message;
            
            // Log the message to logger
            Log::error("An error occurred while updating certificate!", [
                'error' => $message,
                'certificate' => [
                    'id' => $certificate->id,
                    'ref' => $certificate->ref,
                    'status' => $old_status,
                    'filename' => $filename
                ],
                'new_status' => $status
            ]);

            // Return error JSON response
            return response()->json($this->error('An error occurred while updating certificate!'), 500);
        }

        // Certificate was updated successfully
        return $this->success('Certificate was updated successfully!', [
            'certificate' => [
                'ref' => $certificate->ref,
                'status' => $certificate->status,
                'image' => url(Storage::url('certificates/' . $filename))
            ]
        ]);
    }

    public function delete(Certificate $certificate) {
        $filename = $certificate->ref . ".jpg"; // Certificate image

        // Try to delete certificate image from storage
        try {
            Storage::delete('certificates/' . $filename);
            Storage::delete('certificates/thumbnails/' . $filename);
        }
        // Return error response and log the event to logger
        catch (Exception $e) {
            $message = $e->getMessage();
            $message = empty($message) || is_null($message) ? "Unknown error" : $message;
            
            // Log the message to logger
            Log::error("An error occurred while deleting certificate image", [
                'error' => $message,
                'certificate' => [
                    'id' => $certificate->id,
                    'ref' => $certificate->ref,
                    'image' => Storage::url('certificates/' . $filename)
                ]
            ]);

            // Return error JSON response
            return response()->json($this->error('An error occurred while deleting certificate image'), 500);
        }

        // Try to delete certificate record from database
        try {
            $certificate->delete();
        }
        // Return error response and log the event to logger
        catch (Exception $e) {
            $message = $e->getMessage();
            $message = empty($message) || is_null($message) ? "Unknown error" : $message;
            
            // Log the message to logger
            Log::error("An error occurred while deleting certificate!", [
                'error' => $message,
                'id' => $certificate->id
            ]);

            // Return error JSON response
            return response()->json($this->error('An error occurred while deleting certificate!'), 500);
        }

        // Certificate was deleted successfully
        return $this->success('Certificate was deleted successfully!');
    }

    private function error(string $message, array $data = []): array {
        $result = [
            'success' => false,
            'message' => $message
        ];

        if (count($data) > 0) $result['data'] = $data;
        return $result;        
    }

    private function success(string $message, array $data = []): array {
        $result = [
            'success' => true,
            'message' => $message
        ];

        if (count($data) > 0) $result['data'] = $data;
        return $result;        
    }
}
