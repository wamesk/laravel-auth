<?php

namespace Wame\LaravelAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Wame\ApiResponse\Helpers\ApiResponse;
use Wame\LaravelAuth\Http\Controllers\Traits\HasPasswordReset;
use Wame\LaravelAuth\Http\Resources\V1\BaseUserResource;
use Wame\LaravelAuth\Notifications\UserRegisteredNotification;

class LaravelAuthController extends Controller
{
    use HasPasswordReset;

    /** @var string  */
    protected string $codePrefix = 'auth';

    /**
     * @param Request $request
     * @return JsonResponse|ApiResponse
     * @throws GuzzleException
     */
    public function login(Request $request): JsonResponse|ApiResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())
                ->code('1.1.1', $this->codePrefix)
                ->response(400);
        }

        $passport = $this->authUser($request->email, $request->password);

        if (isset($passport['error'])) {
            if ($passport['error'] == 'invalid_grant') {
                return ApiResponse::code('1.1.2', $this->codePrefix)->response(403);
            }
        }

        $user = User::where('email', $request->email)->first();

        if (config('wame-auth.login.require_email_verified') && !$user->hasVerifiedEmail()) {
            return ApiResponse::code('1.1.2', $this->codePrefix)->response(403);
        }

        $data['user'] = new BaseUserResource($user);
        $data['auth'] = $passport;

        return ApiResponse::data($data)->code('1.1.2', $this->codePrefix)->response(200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => config('wame-auth.register.password.rules')
        ]);

        if ($validator->fails()) {
            return ApiResponse::errors($validator->messages()->toArray())
                ->code('1.1.1', $this->codePrefix)
                ->response(400);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => null
            ]);

            if (config('wame-auth.register.send_verification_mail')) {
                $user->notify(new UserRegisteredNotification());
            }

            $passport = $this->authUser($request->email, $request->password);

            $data['user'] = new BaseUserResource($user);
            $data['auth'] = $passport;
        }

        return ApiResponse::data($data)->code('1.1.2', $this->codePrefix)->response(201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|void
     */
    public function verifyEmail(Request $request) {
        if (\Illuminate\Support\Facades\URL::hasValidSignature($request)) {
            $user = User::find($request->id);

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }
            return view('wame-auth::emails.verify', ['user' => $user]);
        } else {
            return view('wame-auth::emails.expiredVerification');
        }
    }

    /**
     * @param string $email
     * @param string $password
     * @return mixed|void
     * @throws GuzzleException
     */
    private function authUser(string $email, string $password) {
        $client = new Client([
            'http_errors' => false
        ]);

        try {
            $response = $client->post(env('APP_URL') . config('wame-auth.login_endpoint'), [
                "form_params" => [
                    'grant_type' => 'password',
                    'client_id' => config('passport.personal_access_client.id'),
                    'client_secret' => config('passport.personal_access_client.secret'),
                    'username' => $email,
                    'password' => $password,
                    'scope' => ''
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $exception) {

        }
    }
}
