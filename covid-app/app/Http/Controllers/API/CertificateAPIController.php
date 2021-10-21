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
        $user = auth('api')->user();

        // Check if file was successfully uploaded
        if (! $request->hasFile('certificate'))
            return abort(400, 'Invalid or corrupt file provided!');

        
        $image = $request->file('certificate'); // Get the uploaded file
        $uuid = Str::orderedUuid(); // Generate a unique ID for certificate
        $filename = $uuid . "." . /* $image->extension() */ "jpg";

        // Try to save the uploaded file under `/storage/certificates` directory,
        // handle error and send appropriate response to the client
        try {
            // Save the image to specified path
            $path = $image->storeAs('certificates', $filename);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $message = empty($message) || is_null($message) ? "Unknown error" : $message;
            
            // Log the message to logger
            Log::error("An error occurred while saving certificate file!", [
                'error' => $message
            ]);
    
            // Return error JSON response
            return response()->json([
                'success' => false,
                'error' => "An error occurred while saving certificate file!",
            ], 500);
        }

        // Try to create new record for certificate in the database, handle error
        // and send appropriate response to the client
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
                'error' => "An error occurred while saving certificate!",
            ], 500);
        }

        // Certificate was added successfully!
        return response()->json([
            'success' => true,
            'error' => null,
            'data' => [
                'certificate' => [
                    'id' => $certificate->id,
                    'ref' => $certificate->ref, // Unique certificate ID
                    'image' => Storage::url('certificates/' . $filename)
                ]
            ]
        ]);
    }

    public function show(Certificate $certificate) {
        $user = auth('api')->user();

        // Provider can only view his own certificate
        if ($user->id !== $certificate->user()->id) abort(403);

        $filename = $certificate->ref . ".jpg";

        // Respond with the certificate uid and image path
        return response()->json([
            'success' => true,
            'error' => null,
            'data' => [
                'certificate' => [
                    'id' => $certificate->id,
                    'ref' => $certificate->ref, // Unique certificate ID
                    'image' => Storage::url('certificates/' . $filename)
                ]
            ]
        ]);
    }

    /**
     * Admin accessible methods
     */
    public function index(Request $request) {
        return Certificate::all();
    }

    public function update(Request $request, Certificate $certificate) {
        $status = $request->input('status', null);

        // Only status can be updated
        if (! is_string($status) || ! preg_match('/$(approved|rejected)^/'))
            return abort(400, 'Provided invalid value for "status" field!');
        
        $old_status = $certificate->status;

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
                'certificate' => $certificate->id,
                'old_status' => $old_status,
                'new_status' => $certificate->status
            ]);

            // Return error JSON response
            return response()->json([
                'success' => false,
                'error' => $message,
            ], 500);
        }

        // Certificate was updated successfully
        return response()->json([
            'success' => true,
            'error' => null,
            'data' => [
                'certificate' => $certificate
            ]
        ]);
    }

    public function delete(Certificate $certificate) {
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
                'error' => $message,
            ], 500);
        }

        // Certificate was deleted successfully
        return response()->json([
            'success' => true,
            'error' => null,
            'data' => [
                'certificate' => $certificate
            ]
        ]);
    }
}
