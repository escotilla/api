<?php

namespace App\Http\Controllers;

use App\Application;
use App\UploadedFile;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends EscotillaController
{
    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|max:10000|mimes:doc,docx,pdf,jpg,png,txt,jpeg',
            'application_id' => 'required'
        ]);

        $user = Auth::user();
        $application = Application::find($request->get('application_id'));

        if (is_null($application)) {
            return $this->errorResponse('application not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $credentials = new Credentials(
                config('filesystems.disks.s3.key'),
                config('filesystems.disks.s3.secret')
            );

            $s3 = new S3Client([
                'version'     => 'latest',
                'region'      => 'us-west-1',
                'credentials' => $credentials
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $originalFileName = $file->getClientOriginalName();
                $fileSize = $file->getClientSize();

                $fileExtension = $file->getClientOriginalExtension();
                $savedFileName = uniqid('file-') . '.' . $fileExtension;

                Storage::disk('s3')->put($savedFileName, $file);

                $uploadedFile = new UploadedFile([
                    'original_file_name' => $originalFileName,
                    'file_name' => $savedFileName,
                    'size' => $fileSize
                ]);

                $uploadedFile->applications()->save($application);
                $user->uploaded_files()->save($uploadedFile);
            }
        } catch (\Exception $error) {
            return $this->errorResponse($error->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->successResponse($user->to_auth_output());
    }
}