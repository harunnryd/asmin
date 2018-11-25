<?php namespace App\Http\Controllers;

use League\Fractal\Manager;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

class ApiController extends Controller 
{
    const CODE_WRONG_ARGS = 'GEN-FUBARGS';
    const CODE_NOT_FOUND = 'GEN-LIKETHEWIND';
    const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    const CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';
    const CODE_FORBIDDEN = 'GEN-GTFO';

    const DELIMITER_PATTERN = '/,/';

    protected $statusCode = 200;
    protected $includes = [];

    public function __construct(Manager $fractal, Request $request) {
        $this->fractal = $fractal;
        $this->includes = preg_split(self::DELIMITER_PATTERN, $request->input('includes'));
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setIncludes(array $includes = []) {
        $this->includes = $includes;
        return $this;
    }

    protected function respondWithItem($item, $callback) {
        $resource = new Item($item, $callback);
        $rootScope = $this->fractal->parseIncludes($this->includes)->createData($resource);
        return $this->respondWithArray($rootScope->toArray());
    }

    protected function respondWithCollection($collection, $callback) {
        $resource = new Collection($collection, $callback);
        $rootScope = $this->fractal->parseIncludes($this->includes)->createData($resource);
        return $this->respondWithArray($rootScope->toArray());
    }

    protected function respondWithArray(array $arr, array $headers = []) {
        return response()->json($arr, $this->statusCode, $headers);
    }

    protected function respondWithError($message, $errorCode) {
        if ($this->statusCode === 200) {
            trigger_error(
                "You better have a really good reason for erroring on a 200...",
                E_USER_WARNING
            );
        }

        return $this->respondWithArray([
            'error' => [
                'code' => $errorCode,
                'http_code' => $this->statusCode,
                'message' => $message
            ]
        ]);
    }

    public function errorForbidden($message = 'Forbidden') {
        return $this->setStatusCode(403)->respondWithError($message, self::CODE_FORBIDDEN);
    }

    public function errorInternalError($message = 'Internal Error') {
        return $this->setStatusCode(500)->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    public function errorNotFound($message = 'Resource Not Found') {
        return $this->setStatusCode(404)->respondWithError($message, self::CODE_NOT_FOUND);
    }

    public function errorUnauthorized($message = 'Unauthorized') {
        return $this->setStatusCode(401)->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    public function errorWrongArgs($message = 'Wrong Arguments') {
        return $this->setStatusCode(400)->respondWithError($message, self::CODE_WRONG_ARGS);
    }
}