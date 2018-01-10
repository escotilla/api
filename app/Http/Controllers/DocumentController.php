<?php

namespace App\Http\Controllers;

use App\Application;
use App\UploadedFile;
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

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            try {
                $uploadedFile = $this->saveFileToS3($file, $application);
                $user->uploaded_files()->save($uploadedFile);
            } catch (\Exception $error) {
                return $this->errorResponse($error->getMessage(), Response::HTTP_BAD_REQUEST);
            }
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
            } else {
                return $this->errorResponse('File not found', Response::HTTP_NOT_FOUND);
            }

        } catch (\Exception $error) {
            return $this->errorResponse($error->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return response()->download($file);
    }

    /**
     * @param $file
     * @param $application
     * @return UploadedFile
     */
    public function saveFileToS3($file, $application): UploadedFile
    {
        $originalFileName = $file->getClientOriginalName();
        $fileSize = $file->getClientSize();

        $fileExtension = $file->getClientOriginalExtension();
        $savedFileName = uniqid('file-') . '.' . $fileExtension;

        Storage::disk('s3')->putFileAs($application->user_id, $file, $savedFileName);

        $uploadedFile = new UploadedFile([
            'original_file_name' => $originalFileName,
            'file_name' => $savedFileName,
            'size' => $fileSize
        ]);

        $uploadedFile->applications()->save($application);
        return $uploadedFile;
    }
}