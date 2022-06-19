<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Consts\ConstUser;
use App\Rules\AlphaNumHalf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\LineMessengerController;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $validationConditionList = [
            'parentName'   => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'tel'          => ['required', 'numeric', 'digits_between:10,11'],
            'password'     => ['required', new AlphaNumHalf, 'min:8', 'confirmed'],
            'childName'    => ['required', 'string', 'max:255'],
            'age'          => ['required', 'integer'],
            'gender'       => ['required', 'string', 'max:255'],
            'diagnosis'    => ['nullable', 'string', 'max:255'],
            'childName2'   => ['nullable', 'string', 'max:255'],
            'age2'         => ['nullable', 'integer'],
            'gender2'      => ['nullable', 'string', 'max:255'],
            'diagnosis2'   => ['nullable', 'string', 'max:255'],
            'postCode'     => ['required', 'string', 'min:7', 'max:8'],
            'address'      => ['required', 'string', 'max:255'],
            'coursePlan'   => ['required', 'integer'],
            'introduction' => ['nullable', 'string', 'max:255'],
            'consaltation' => ['nullable', 'string', 'max:255'],
        ];

        if (!empty($data['childName2'])) {
            array_unshift($validationConditionList['age2'], 'required');
            array_unshift($validationConditionList['gender2'], 'required');
        }

        return Validator::make($data, $validationConditionList);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  Array  $data
     * @return App\Models\User
     */
    protected function create(array $data)
    {
        if (empty($data['lineConsultation'])) $data['lineConsultation'] = false;
        
        $fee = $this->getFeeByCourse((int) $data['numberOfUse'], (int) $data['coursePlan'], $data['childName2'], $data['lineConsultation']);
        $useTime = $this->getUseTimeByFee($fee);

        // 利用時2未入力時は、gender2をnullに設定
        // ※gender2はラジオボタンであり初期値が設定されるため
        if (empty($data['childName2'])) $data['gender2'] = null;

        $user = User::create([
            'parentName'                 => $data['parentName'],
            'email'                      => $data['email'],
            'tel'                        => $data['tel'],
            'password'                   => Hash::make($data['password']),
            'childName'                  => $data['childName'],
            'age'                        => $data['age'],
            'gender'                     => $data['gender'],
            'diagnosis'                  => $data['diagnosis'],
            'childName2'                 => $data['childName2'],
            'age2'                       => $data['age2'],
            'gender2'                    => $data['gender2'],
            'diagnosis2'                 => $data['diagnosis2'],
            'address'                    => $data['address'],
            'introduction'               => $data['introduction'],
            'coursePlan'                 => $data['coursePlan'],
            'consaltation'               => $data['consaltation'],
            'fee'                        => $fee,
            'userAgent'                  => $_SERVER['HTTP_USER_AGENT'],
            'use_time'                   => $useTime,
            'line_consultation_flag'     => $data['lineConsultation'],
        ]);

        $lineModel = new LineMessengerController();
        $lineModel->sendRegistrationMessage($user);

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath())->with('registration', '会員登録に成功しました。</br>ご予約をご希望の方は予約画面からお願い致します。');
    }

    /**
     * 選択されたコースの条件によって料金取得
     * @param Integer　 利用人数
     * @param Integer　 コースプラン
     * @param Integer　 兄弟児利用
     * @param Boolean　 ラインのみ利用フラグ
     * @return Integer 料金
     */
    private function getFeeByCourse($numberOfUse, $coursePlan, $siblingUse, $lineConsultation)
    {
        // LINEのみ相談
        if ($lineConsultation) return ConstUser::FEE_LINE_ONLY;

        if ($numberOfUse === ConstUser::ONE_USE) {
            if (!empty($siblingUse)) return ConstUser::FEE_ONE_SIBLING;
            if ($coursePlan === ConstUser::COURSE_WEEKDAY) return ConstUser::FEE_ONE_WEEKDAY;
            if ($coursePlan === ConstUser::COURSE_HOLIDAY) return ConstUser::FEE_ONE_HOLIDAY;
        } else if ($numberOfUse === ConstUser::TWO_USE) {
            if (!empty($siblingUse)) return ConstUser::FEE_TWO_SIBLING;
            if ($coursePlan === ConstUser::COURSE_WEEKDAY) return ConstUser::FEE_TWO_WEEKDAY;
            if ($coursePlan === ConstUser::COURSE_HOLIDAY) return ConstUser::FEE_TWO_HOLIDAY;
        }
    }

    /**
     * 利用料金によって利用時間取得
     * @param Integer　 利用人数
     * @return Integer 料金
     */
    private function getUseTimeByFee($fee)
    { 
        if ($fee === ConstUser::FEE_ONE_SIBLING || $fee === ConstUser::FEE_TWO_SIBLING) {
            return ConstUser::LONG_USE_TIME;
        } else {
            return ConstUser::NORMAL_USE_TIME;
        }
    }
}
