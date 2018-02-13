<?php

namespace App\Http\Controllers;

use App\Application;
use App\UploadedFile;
use App\User;
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

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            try {
                $this->saveFileToS3($user, $file);
            } catch (\Exception $error) {
                return $this->errorResponse($error->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        $application = Application::find($request->get('application_id'));

        if (!is_null($application)) {
            $application->updateChecklist('upload_documents', 'complete');
        }

        return $this->successResponse($user->to_auth_output());
    }

    public function download(Request $request)
    {
        $this->validate($request, [
            'uploaded_file_id' => 'required'
        ]);

        $user = Auth::user();

        $uploadedFile = UploadedFile::find($request->get('uploaded_file_id'));

        try {
            if (Storage::disk('s3')->has($user->_id . '/' . $uploadedFile->file_name)) {
                $file = Storage::disk('s3')->get($user->_id . '/' . $uploadedFile->file_name);
                $temp = tempnam(sys_get_temp_dir(), $uploadedFile->file_name);
                $handle = fopen($temp, "w");
                fwrite($handle, $file);
                fclose($handle);
            } else {
                return $this->errorResponse('File not found', Response::HTTP_NOT_FOUND);
            }

        } catch (\Exception $error) {
            return $this->errorResponse($error->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $headers = [
            'Content-Type' => $uploadedFile->mime_type
        ];

        return response()
            ->download($temp, $uploadedFile->original_file_name, $headers)
            ->deleteFileAfterSend(true);
    }

    public function saveFileToS3(User $user, $file): UploadedFile
    {
        $originalFileName = $file->getClientOriginalName();
        $fileSize = $file->getClientSize();

        $fileExtension = $file->getClientOriginalExtension();
        $savedFileName = uniqid('file-') . '.' . $fileExtension;

        Storage::disk('s3')->putFileAs($user->_id, $file, $savedFileName);

        $uploadedFile = new UploadedFile([
            'original_file_name' => $originalFileName,
            'file_name' => $savedFileName,
            'size' => $fileSize,
            'mime_type' => $file->getClientMimeType()
        ]);

        $user->uploaded_files()->save($uploadedFile);

        return $uploadedFile;
    }
}