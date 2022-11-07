<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 *
 * @Date 02/06/21
 */
class StaffBaseController extends Controller
{
    /**
     * success response method.
     *
     * @param $result
     * @param $message
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }

    /**
     * @param $error
     * @param array $errorMessages
     * @param null $errorCode
     * @return mixed
     */
    public function sendJsonError($error, $errorMessages = [], $errorCode = null)
    {
        $response = [
            "success" => false,
            "error" => $error,
            "code" => $errorCode
        ];
        if (!empty($errorMessages)) {
            $response["messages"] = $errorMessages;
        }
        return response()->json($response, 200);
    }

}
