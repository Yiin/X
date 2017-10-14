<?php

namespace App\Interfaces\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Validator;
use App\Interfaces\Http\Controllers\AbstractController;
use App\Domain\Service\Auth\AuthService;
use App\Interfaces\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\Http\Requests\Auth\LoginRequest;
use App\Interfaces\Http\Requests\Auth\DemoRequest;

class AuthController extends AbstractController
{
    private $authService;
    private $accountService;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    public function validateField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'sometimes|required',
            'first_name' => 'sometimes|required',
            'last_name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:users,username',
            'site_address' => 'sometimes|required|unique:accounts',
            'password' => 'sometimes|required|confirmed'
        ]);

        return $validator->errors();
    }

    public function register(RegisterRequest $request)
    {
        /**
         * Name of the company, which also will be used as default account name.
         * @var string
         */
        $companyName = $request->get('company_name');

        /**
         * Company email
         * @var string
         */
        $companyEmail = $request->get('company_email');
        $siteAddress = $request->get('site_address');

        /**
         * User details
         */
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');
        $userEmail = $request->get('email');
        $userPassword = $request->get('password');

        $data = $this->authService->register($companyName, $companyEmail, $siteAddress, $firstName, $lastName, $userEmail, $userPassword);

        return response()->json($data);
    }

    public function login(LoginRequest $request)
    {
        $siteAddress = $request->get('site_address');
        $username = $request->get('username');
        $password = $request->get('password');

        $data = $this->authService->attemptLogin($siteAddress, $username, $password);

        return response()->json($data);
    }

    public function refresh(Request $request)
    {
        return response()->json($this->authService->attemptRefresh());
    }

    public function heartbeat()
    {
        if (auth()->check()) {
            return 'OK';
        }
        return response(null, 401);
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json(null, 204);
    }
}