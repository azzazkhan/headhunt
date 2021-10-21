<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Certificate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificateAPIController extends Controller
{
    /**
     * Provider accessible methods
     */
    public function store(Request $request) {
        $user = auth('api')->user(); // Get the current logged in user

        // Make sure the provider does not already created a certificate before!
        if ($user->certificate()->first())
            return response()->json([
                'success' => false,
                'message' => 'Provider already has submitted a certificate!'
            ], 400);

        // Make sure the file was uploaded successfully
        if (! $request->hasFile('certificate') || !$request->file('certificate')->isValid())
            return response()->json([
                'success' => false,
                'message' => 'Invalid or corrupt file provided!'
            ], 400);

        
        $image = $request->file('certificate'); // Get the uploaded file
        $uuid = Str::orderedUuid(); // Generate a unique ID for certificate
        $filename = $uuid . "." . /* $image->extension() */ "jpg";

        // Try to save the uploaded file under `/storage/certificates` directory
        try {
            // Save the image to specified path
            $path = $image->storeAs('certificates', $filename);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $message = empty($message) || is_null($message) ? "Unknown error" : $message;
            
            // Log the message to logger
            Log::error("An error occurred while saving certificate file!", [
                'error' => $message,
                'filename' => $filename
            ]);
    
            // Return error JSON response
            return response()->json([
                'success' => false,
                'message' => "An error occurred while saving certificate!",
            ], 500);
        }

        // Try to create new record for certificate in the database
        try {
            // Create a new certificate for current logged in user
            $certificate = $user->certificate()->create([
                'ref' => $uuid,
                'user_id' => $user->id
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
            return response()->json([
                'success' => false,
                'message' => "An error occurred while saving certificate!",
            ], 500);
        }

        // Certificate was added successfully!
        return [
            'success' => true,
            'data' => [
                'certificate' => [
                    'ref' => $certificate->ref, // Unique certificate ID
                    'image' => url(Storage::url('certificates/' . $filename))
                ]
            ],
            'message' => 'Certificate was created successfully!'
        ];
    }

    public function show(Certificate $certificate) {
        $user = auth('api')->user();

        // Provider can only view his own certificate
        if ($user->id !== $certificate->user()->first()->id)
            return response()->json([
                'success' => false,
                'message' => 'Cannot view someone else\'s certificate!'
            ], 403);

        $filename = $certificate->ref . ".jpg";

        // Respond with the certificate uid and image path
        return [
            'success' => true,
            'data' => [
                'certificate' => [
                    'ref' => $certificate->ref, // Unique certificate ID
                    'image' => url(Storage::url('certificates/' . $filename))
                ]
            ],
            'message' => 'Successfully retrieved certificate!'
        ];
    }

    /**
     * Admin accessible methods
     */
    public function index(Request $request) {
        return [
            'success' => true,
            'data' => [
                // This method will spit out additional data like timestamps
                // and primary key but it's okay because this will be sent
                // to super admin only
                'certificates' => Certificate::all()
            ],
            'message' => 'Successfully retrieved all certificates!'
        ];
    }

    public function update(Request $request, Certificate $certificate) {
        $status = $request->input('status', null);

        // Status must be present with one of two values (`approved` or `rejected`)
        if (! is_string($status) || ! preg_match('/$(approved|rejected)^/', $status))
            return response()->json([
                'success' => false,
                'message' => 'Provided invalid value for "status" field!'
            ], 400);
        
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
            return response()->json([
                'success' => false,
                'message' => "An error occurred while updating certificate!",
            ], 500);
        }

        // Certificate was updated successfully
        return [
            'success' => true,
            'data' => [
                'certificate' => [
                    'ref' => $certificate->ref,
                    'image' => url(Storage::url('certificates/' . $filename))
                ]
            ],
            'message' => 'Certificate was updated successfully!'
        ];
    }

    public function delete(Certificate $certificate) {
        $filename = $certificate->ref . ".jpg"; // Certificate image

        // Try to delete certificate image from storage
        try {
            Storage::delete('certificates/' . $filename);
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
            return response()->json([
                'success' => false,
                'message' => "An error occurred while deleting certificate image",
            ], 500);
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
            return response()->json([
                'success' => false,
                'message' => "An error occurred while deleting certificate!",
            ], 500);
        }

        // Certificate was deleted successfully
        return [
            'success' => true,
            'message' => 'Certificate was deleted successfully!'
        ];
    }
}
