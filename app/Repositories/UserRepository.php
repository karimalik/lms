<?php

namespace App\Repositories;


use App\Models\LmsInstitute;
use App\Subscription;
use App\Traits\ImageStore;
use App\User;
use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Modules\Affiliate\Repositories\AffiliateRepository;
use Modules\Coupons\Entities\UserWiseCoupon;
use Modules\LmsSaas\Http\Controllers\LmsSaasController;
use Modules\Newsletter\Entities\NewsletterSetting;
use Modules\Newsletter\Http\Controllers\AcelleController;
use Modules\RolePermission\Entities\Role;
use Modules\SystemSetting\Entities\Staff;
use Modules\SystemSetting\Entities\StaffDocument;

class UserRepository implements UserRepositoryInterface
{
    use ImageStore;


    public function create(array $data)
    {

        $user = User::create($data);

        $user->dob = $data['dob'] ?? null;
        $user->gender = $data['gender'] ?? null;
        $user->student_type = $data['student_type'] ?? null;
        $user->job_title = $data['job_title'] ?? null;
        $user->identification_number = $data['identification_number'] ?? null;
        $user->company_id = $data['company_id'] ?? null;

        $user->referral = generateUniqueId();
        $user->save();

        if(isModuleActive('Affiliate')){
            $affiliateRepo = new AffiliateRepository();
            $affiliateRepo->affiliateUser($user->id);
        }


        assignStaffToUser($user);

        if (isset($data['is_lms_signup'])) {
            $institute = new LmsInstitute();
            $institute->name = $data['institute_name'];
            $institute->domain = $data['domain'];
            $institute->user_id = $user->id;
            $institute->save();
            $user->lms_id = $institute->id;
            $user->update();
            $saas_controller = new LmsSaasController();
            $saas_controller->lmsSetup($institute->id);
        } else {
            if (session::get('referral') != null) {
                $invited_by = User::where('referral', session::get('referral'))->first();
                $user_coupon = new UserWiseCoupon();
                $user_coupon->invite_by = $invited_by->id;
                $user_coupon->invite_accept_by = $user->id;
                $user_coupon->invite_code = session::get('referral');
                $user_coupon->save();
            }


            $mailchimpStatus = saasEnv('MailChimp_Status') ?? false;
            $getResponseStatus = saasEnv('GET_RESPONSE_STATUS') ?? false;
            $acelleStatus = saasEnv('ACELLE_STATUS') ?? false;
            if (hasTable('newsletter_settings')) {
                $setting = NewsletterSetting::getData();
                if ($data['role_id'] == 2) {

                    if ($setting->instructor_status == 1) {
                        $list = $setting->instructor_list_id;
                        if ($setting->instructor_service == "Mailchimp") {

                            if ($mailchimpStatus) {
                                try {
                                    $MailChimp = new MailChimp(saasEnv('MailChimp_API'));
                                    $MailChimp->post("lists/$list/members", [
                                        'email_address' => $data['email'],
                                        'status' => 'subscribed',
                                    ]);

                                } catch (\Exception $e) {
                                }
                            }
                        } elseif ($setting->instructor_service == "GetResponse") {
                            if ($getResponseStatus) {

                                try {
                                    $getResponse = new \GetResponse(saasEnv('GET_RESPONSE_API'));
                                    $getResponse->addContact(array(
                                        'email' => $data['email'],
                                        'campaign' => array('campaignId' => $list),
                                    ));
                                } catch (\Exception $e) {

                                }
                            }
                        } elseif ($setting->instructor_service == "Acelle") {
                            if ($acelleStatus) {

                                try {
                                    $email = $data['email'];
                                    $make_action_url = '/subscribers?list_uid=' . $list . '&EMAIL=' . $email;
                                    $acelleController = new AcelleController();
                                    $response = $acelleController->curlPostRequest($make_action_url);
                                } catch (\Exception $e) {

                                }
                            }
                        } elseif ($setting->instructor_service == "Local") {
                            try {
                                $check = Subscription::where('email', '=', $data['email'])->first();
                                if (empty($check)) {
                                    $subscribe = new Subscription();
                                    $subscribe->email = $data['email'];
                                    $subscribe->type = 'Instructor';
                                    $subscribe->save();
                                } else {
                                    $check->type = "Instructor";
                                    $check->save();
                                }
                            } catch (\Exception $e) {

                            }
                        }
                    }


                } elseif ($data['role_id'] == 3) {
                    if ($setting->student_status == 1) {
                        $list = $setting->student_list_id;
                        if ($setting->student_service == "Mailchimp") {

                            if ($mailchimpStatus) {
                                try {
                                    $MailChimp = new MailChimp(saasEnv('MailChimp_API'));
                                    $MailChimp->post("lists/$list/members", [
                                        'email_address' => $data['email'],
                                        'status' => 'subscribed',
                                    ]);

                                } catch (\Exception $e) {
                                }
                            }
                        } elseif ($setting->student_service == "GetResponse") {
                            if ($getResponseStatus) {

                                try {
                                    $getResponse = new \GetResponse(saasEnv('GET_RESPONSE_API'));
                                    $getResponse->addContact(array(
                                        'email' => $data['email'],
                                        'campaign' => array('campaignId' => $list),
                                    ));
                                } catch (\Exception $e) {

                                }
                            }
                        } elseif ($setting->student_service == "Acelle") {
                            if ($acelleStatus) {

                                try {
                                    $email = $data['email'];
                                    $make_action_url = '/subscribers?list_uid=' . $list . '&EMAIL=' . $email;
                                    $acelleController = new AcelleController();
                                    $response = $acelleController->curlPostRequest($make_action_url);
                                } catch (\Exception $e) {

                                }
                            }
                        } elseif ($setting->student_service == "Local") {
                            try {
                                $check = Subscription::where('email', '=', $data['email'])->first();
                                if (empty($check)) {
                                    $subscribe = new Subscription();
                                    $subscribe->email = $data['email'];
                                    $subscribe->type = 'Student';
                                    $subscribe->save();
                                } else {
                                    $check->type = "Student";
                                    $check->save();
                                }
                            } catch (\Exception $e) {

                            }
                        }
                    }

                }
            }

        }


        if (Settings('email_verification') != 1) {
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
        } else {
            if (isModuleActive('LmsSaas') && !empty($user->institute) && $user->institute->domain != SaasDomain()) {
                Storage::put(md5($user->email), $user->email);
            } else {
                $user->sendEmailVerificationNotification();
            }
//            $user->sendEmailVerificationNotification();
        }

        return $user;
    }

    public function store(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->username = $data['username'];
        $user->role_id = $data['role_id'];
        $user->country = $data['country'];
        if (isset($data['photo'])) {
            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['photo']));
            $user->image = $data['avatar'];
        }
        $user->password = Hash::make($data['password']);
        if (Settings('email_verification') != 1) {
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
        } else {
            $user->sendEmailVerificationNotification();
        }
        return $user;
    }


    public function update(array $data, $id)
    {
        $user = User::findOrFail($id);
        if (Hash::check($data['password'], Auth::user()->password)) {
            if (isset($data['photo'])) {
                $data = Arr::add($data, 'avatar', $this->saveAvatar($data['photo']));
                $user->image = $data['avatar'];
            }
            $user->name = $data['name'];
            $user->username = $data['username'];
            $user->role_id = $data['role_id'];
            $user->password = Hash::make($data['password']);
            if ($user->save()) {
                $staff = $user->staff;
                $staff->user_id = $user->id;
                $staff->department_id = $data['department_id'];
                $staff->employee_id = $data['employee_id'];
                $staff->showroom_id = $data['showroom_id'];
                // $staff->warehouse_id = $data['warehouse_id'];
                $staff->phone = $data['phone'];
                if ($staff->save()) {
                    if (Settings('email_verification') != 1) {
                        $user->email_verified_at = date('Y-m-d H:m:s');
                        $user->save();
                    } else {
                        $user->sendEmailVerificationNotification();
                    }
                }
                return $user;
            }
        }
    }


    ///////


    public function user()
    {
        return User::with('leaves', 'leaveDefines')->latest()->get();
    }

    public function all($relational_keyword = [])
    {
        if (count($relational_keyword) > 0) {
            return Staff::latest()->get();
        } else {
            return Staff::latest()->get();
        }

    }

    public function find($id)
    {
        return Staff::with('user')->findOrFail($id);
    }

    public function findUser($id)
    {
        return User::findOrFail($id);
    }

    public function findDocument($id)
    {
        return StaffDocument::where('staff_id', $id)->get();
    }


    public function updateProfile(array $data, $id)
    {
        $user = User::findOrFail($id);
        if (isset($data['avatar'])) {
            $user->avatar = $this->saveAvatar($data['avatar'], 60, 60);
        }
        $user->name = $data['name'];
        if (isset($data['password']) and $data['password']) {
            $user->password = bcrypt($data['password']);
        }

        $result = $user->save();
        $staff = $user->staff;
        if ($result) {
            $staff->phone = $data['phone'];
            if ($user->role_id != 1) {
                $staff->bank_name = $data['bank_name'];
                $staff->bank_branch_name = $data['bank_branch_name'];
                $staff->bank_account_name = $data['bank_account_name'];
                $staff->bank_account_no = $data['bank_account_no'];
                $staff->current_address = $data['current_address'];
                $staff->permanent_address = $data['permanent_address'];
            }

            $staff->save();
        }
        return $staff;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        if ($user->staff) {
            if ($user->staff->payrolls) {
                $user->staff->payrolls()->delete();
            }
            $user->staff->delete();
        }


        $user->delete();
    }

    public function statusUpdate($data)
    {
        $user = User::find($data['id']);
        $user->is_active = $data['status'];
        $user->status = $data['status'];
        $user->save();
    }

    public function deleteStaffDoc($id)
    {
        $document = StaffDocument::findOrFail($id)->delete();
    }

    public function normalUser()
    {
        $normal_roles_id = Role::where('type', 'regular_user')->pluck('id');
        return User::where('id', Auth::id())->orwhereIn('role_id', $normal_roles_id)->get();
    }

    public function roleUsers($role_id)
    {
        return User::where('role_id', $role_id)->where('is_active', 1)->get();
    }


}
