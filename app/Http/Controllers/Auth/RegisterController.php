<?php

namespace App\Http\Controllers\Auth;

use App\StudentCustomField;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use Modules\FrontendManage\Entities\LoginPage;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (saasEnv('nocaptcha_for_reg')) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'g-recaptcha-response' => 'required|captcha'
            ];
        } else {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ];
        }

        if (isset($data['is_lms_signup'])) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'institute_name' => ['required', 'string', 'max:255'],
                'domain' => ['required', 'string', 'max:20', 'unique:lms_institutes'],
            ];
        }

        return Validator::make($data, $rules,
            validationMessage($rules)
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        if (isset($data['type']) && $data['type'] == "Instructor") {
            $role = 2;
        } else {
            $role = 3;
        }
        if (isset($data['is_lms_signup'])) {
            $role = 1;
        }

        if (empty($data['phone'])) {
            $data['phone'] = null;
        }
        return $this->userRepository->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'role_id' => $role,
            'password' => Hash::make($data['password']),
            'language_id' => Settings('language_id') ?? '19',
            'language_name' => Settings('language_name') ?? 'English',
            'language_code' => Settings('language_code') ?? 'en',
            'language_rtl' => Settings('language_rtl') ?? '0',
            'country' => Settings('country_id'),
            'username' => null,
            'is_lms_signup' => $data['is_lms_signup'] ?? null,
            'institute_name' => $data['institute_name'] ?? null,
            'domain' => str_replace(' ', '', $data['domain'] ?? null),
            'referral' => generateUniqueId(),
        ]);
    }

    public function RegisterForm()
    {
        abort_if(!Settings('student_reg'), 404);
        abort_if(saasPlanCheck('student'), 404);
        $page = LoginPage::getData();
        $custom_field = StudentCustomField::getData();
        return view(theme('auth.register'), compact('page', 'custom_field'));
    }

    public function LmsRegisterForm()
    {
        abort_if(!isModuleActive('LmsSaas'), 404);
        abort_if(SaasDomain() != 'main', 404);
        $page = LoginPage::getData();
        $custom_field = StudentCustomField::getData();
        return view(theme('auth.lms_register'), compact('page', 'custom_field'));
    }

    public function showRegistrationForm()
    {
        $page = LoginPage::getData();
        return view(theme('auth.register'), compact('page'));
    }


    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());

        if (isModuleActive('LmsSaas') && !empty($user->institute) && $user->institute->domain != SaasDomain()) {
            if ($user->lms_id != 1) {
                $token = md5(uniqid());
                Storage::put($token, $request->email . '|' . $request->password);
                $url = 'http://' . $user->institute->domain . '.' . config('app.short_url') . '/login?token=' . $token;
                return redirect()->to($url);
            }
        }
        event(new Registered($user));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }
}
