<?php

namespace App\Traits;

/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
	/**
     * Return a success JSON response.
     *
     * @param  array|string  $data
     * @param  string  $message
     * @param  int|null  $code
     * @return \Illuminate\Http\JsonResponse
     */
     public function success($data = null, string $message = null, int $code = 200)
     {
          return [
               'success' => true,
               'ecode' => $code,
               'message' => $message,
               'data' => $data
          ];
     }

	/**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|string|null  $data
     * @return \Illuminate\Http\JsonResponse
     */
     public function error($data = null, string $message = null, int $code = 401)
     {
          return [
               'success' => false,
               'ecode' => $code,
               'message' => $message,
               'data' => $data
          ];
     }

}