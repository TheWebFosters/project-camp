<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $statusCode;

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respondWithError($message = null)
    {
        return response()->json(
            ['success' => false, 'msg' => $message]
        );
    }

    /**
     * Returns a Unauthorized response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\Response
     */
    public function respondUnauthorized($message = 'Unauthorized action.')
    {
        return $this->setStatusCode(403)
            ->respondWithError($message);
    }

    /**
     * Returns a went wrong response.
     *
     * @param  object  $exception = null
     * @return \Illuminate\Http\Response
     */
    public function respondWentWrong($exception = null)
    {
        //If debug is enabled then send exception message
        $message = (config('app.debug') && is_object($exception)) ? "File:" . $exception->getFile(). "Line:" . $exception->getLine(). "Message:" . $exception->getMessage() : __('messages.something_went_wrong');

        //TODO: show exception error message when error is enabled.
        return $this->setStatusCode(200)
            ->respondWithError($message);
    }

    /**
     * Returns a 200 response.
     *
     * @param  object  $message = null
     * @return \Illuminate\Http\Response
     */
    public function respondSuccess($message = null, $additional_data = [])
    {
        $message = is_null($message) ? __('messages.success') : $message;
        $data = ['success' => true, 'msg' => $message];

        if (!empty($additional_data)) {
            $data = array_merge($data, $additional_data);
        }

        return $this->respond($data);
    }

    /**
     * Returns a 200 response.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function respond($data)
    {
        return response()->json($data);
    }

    /**
     * Returns a disabled response for demo
     *
     * @return \Illuminate\Http\Response
     */
    public function respondDemo()
    {
        $data = ['success' => false, 'msg' => __('messages.feature_disabled_in_demo')];
        return $this->respond($data);
    }

    public function isDemo()
    {
        if (config('app.env') == 'demo') {
            return true;
        }
    }

    /**
     * This function add's medias to the supplied model.
     *
     * @param  $medias
     * @param  $model object, instance of model to which media need to be added
     */
    public function addMedias($medias, $model, $mediaCollectionName)
    {
        if (!empty($medias)) {
            foreach ($medias as $key => $file_name) {
                $model->addMedia(public_path('uploads/'.config('constants.temp_upload_folder').'/'.$file_name))
                    ->toMediaCollection($mediaCollectionName);
            }
        }
    }
}
