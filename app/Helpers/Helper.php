<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\HumanResource\Entities\Attendance;
use Modules\OrgSubscription\Entities\OrgCourseSubscription;
use Modules\OrgSubscription\Entities\OrgSubscriptionCheckout;
use Modules\Payment\Entities\Cart;
use Modules\Setting\Model\Currency;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Modules\Setting\Entities\IpBlock;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Modules\Appearance\Entities\Theme;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Model\GeneralSetting;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use Modules\CourseSetting\Entities\Category;
use Modules\FrontendManage\Entities\HomeContent;
use Modules\SystemSetting\Entities\EmailTemplate;
use Modules\FrontendManage\Entities\CourseSetting;
use Modules\StudentSetting\Entities\BookmarkCourse;
use Modules\Subscription\Entities\SubscriptionCheckout;
use Modules\NotificationSetup\Entities\RoleEmailTemplate;
use Modules\NotificationSetup\Entities\UserNotificationSetup;

if (!function_exists('send_smtp_mail')) {
    function send_smtp_mail($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
    {
        $mail_val = [
            'send_to_name' => $receiver_name,
            'send_to' => $receiver_email,
            'email_from' => $config->from_email,
            'email_from_name' => $config->from_name,
            'subject' => $subject,
        ];

        Mail::send('partials.email', ['body' => $message], function ($send) use ($mail_val) {
            $send->from($mail_val['email_from'], $mail_val['email_from_name']);
            $send->replyto($mail_val['email_from'], $mail_val['email_from_name']);
            $send->to($mail_val['send_to'])->subject($mail_val['subject']);
        });
    }

}

if (!function_exists('sendMailBySendGrid')) {
    function sendMailBySendGrid($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($config->from_email, $config->from_name);
        $email->setSubject($subject);
        $email->addTo($receiver_email, $receiver_email);
        $email->addContent(
            "text/html", (string)view('partials.email', ['body' => $message])
        );
        $sendgrid = new \SendGrid($config->api_key);
        try {
            $response = $sendgrid->send($email);
            if ($response->statusCode() == 202) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}


if (!function_exists('shortcode_replacer')) {

    function shortcode_replacer($shortcode, $replace_with, $template_string)
    {
        if ($shortcode == "{{currency}}") {
            return str_replace($shortcode, '', $template_string);
        }

        if ($shortcode == "{{amount}}" || $shortcode == "{{price}}" || $shortcode == "{{rev}}") {
            return str_replace($shortcode, getPriceFormat($replace_with), $template_string);
        }
        return str_replace($shortcode, $replace_with, $template_string);
    }
}

if (!function_exists('send_email')) {

    function send_email($user, $type, $shortcodes = [])
    {
        try {
            $status = EmailTemplate::where('act', $type)->first()->status;
            if ($status == 1) {
                $email_template = \Modules\SystemSetting\Entities\EmailTemplate::where('act', $type)->where('status', 1)->first();


                $message = $email_template->email_body;
                foreach ($shortcodes as $code => $value) {
                    $message = shortcode_replacer('{{' . $code . '}}', $value, $message);
                }
                $message = shortcode_replacer('{{footer}}', Settings('email_template'), $message);

                $config = \Modules\SystemSetting\Entities\EmailSetting::where('active_status', 1)->first();

                if ($type == "CONTACT_MESSAGE") {
                    $to_email = Settings('email');

                } else {
                    $to_email = $user->email;

                }

                if ($config->id == 1) {
                    send_php_mail($to_email, $user->name, $config->from_email, $email_template->subj, $message);
                } else if ($config->id == 2) {
                    send_smtp_mail($config, $to_email, $user->name, $config->from_email, Settings('site_title'), $email_template->subj, $message);
                } else if ($config->id == 3) {
                    sendMailBySendGrid($config, $to_email, $user->name, $config->from_email, Settings('site_title'), $email_template->subj, $message);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return false;
        }


    }
}


if (!function_exists('getTrx')) {
    function getTrx($length = 12)
    {
        $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 12; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}


if (!function_exists('routeIs')) {
    function routeIs($route)
    {
        if (Route::currentRouteName() == $route) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('appMode')) {
    function appMode()
    {
        return Config::get('app.app_sync');
    }
}

if (!function_exists('demoCheck')) {
    function demoCheck($message = '')
    {
        if (appMode()) {
            if (empty($message)) {
                $message = trans('common.For the demo version, you cannot change this');
            }
            Toastr::error($message, trans('common.Failed'));
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('userName')) {
    function userName($id)
    {
        if (User::find($id) != null) {
            return User::find($id)->name;
        }
        return null;
    }
}
if (!function_exists('fileUpload')) {
    function fileUpload($file, $destination)
    {
        $contains = Str::contains($destination, SaasDomain() . '/');
        if (!$contains) {
            $destination = explode('public/uploads/', $destination);
            $destination = $destination[0] . 'public/uploads/' . SaasDomain() . '/' . $destination[array_key_last($destination)];
        }


        $fileName = "";

        if (!$file) {
            return $fileName;
        }

        $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $file->move($destination, $fileName);
        $fileName = $destination . $fileName;
        return $fileName;

    }
}

if (!function_exists('fileUpdate')) {
    function fileUpdate($databaseFile, $file, $destination)
    {
        $contains = Str::contains($destination, SaasDomain() . '/');
        if (!$contains) {
            $destination = explode('public/uploads/', $destination);
            $destination = $destination[0] . 'public/uploads/' . SaasDomain() . '/' . $destination[array_key_last($destination)];
        }

        $fileName = "";

        if ($file) {
            $fileName = fileUpload($file, $destination);

            if ($databaseFile && file_exists($databaseFile)) {

                unlink($databaseFile);

            }
        } elseif (!$file and $databaseFile) {
            $fileName = $databaseFile;
        }

        return $fileName;

    }
}
if (!function_exists('showPicName')) {
    function showPicName($data)
    {
        $name = explode('/', $data);
        return $name[array_key_last($name)];
    }
}
if (!function_exists('vimeoVideoEmbed')) {
    function vimeoVideoEmbed($video_uri, $title, $height, $width)
    {
        // return '<iframe class="video_iframe" src="https://player.vimeo.com/video/'.showPicName($video_uri).'?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id='.env("VIMEO_APP_ID").'" width="'.$width.'" height="'.$height.'" frameborder="0" allow="autoplay; fullscreen" allowfullscreen title="LMS Basic"></iframe>';
        return '<iframe class="video_iframe" src="https://player.vimeo.com/video/' . showPicName($video_uri) . '?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=' . saasEnv("VIMEO_APP_ID") . '"  frameborder="0" allow="autoplay; fullscreen" allowfullscreen title="LMS Basic"></iframe>';
    }
}


if (!function_exists('getSetting')) {
    function getSetting()
    {
        try {
            return app('getSetting');

        } catch (Exception $exception) {
            return false;
        }
    }
}
if (!function_exists('getVideoId')) {
    function getVideoId($v_id)
    {
        $video_id = explode("=", $v_id);
        return $video_id[array_key_last($video_id)];
    }
}
if (!function_exists('youtubeVideo')) {
    function youtubeVideo($video_url)
    {
        if (Str::contains($video_url, 'youtu.be')) {

            $url = explode("/", $video_url);
            return 'https://www.youtube.com/watch?v=' . $url[3];
        }

        if (Str::contains($video_url, '&')) {
            return substr($video_url, 0, strpos($video_url, "&"));
        } else {
            return $video_url;
        }


    }
}


if (!function_exists('isBookmarked')) {
    function isBookmarked($user_id, $course_id)
    {
        $bookmarked = BookmarkCourse::where('user_id', $user_id)->where('course_id', $course_id)->first();
        if ($bookmarked) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('cartItem')) {
    function cartItem()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::user()->id)->count();
        } else if (session()->get('cart')) {
            return count(session()->get('cart'));
        } else {
            return 0;
        }
    }
}

if (!function_exists('totalWhiteList')) {
    function totalWhiteList()
    {
        if (Auth::check()) {
            $bookmarks = BookmarkCourse::where('user_id', Auth::id())->count();
            return $bookmarks;
        } else {
            return 0;
        }
    }
}


function send_php_mail($receiver_email, $receiver_name, $sender_email, $subject, $message)
{
    $headers = "From: <$sender_email> \r\n";
    $headers .= "Reply-To: " . Settings('site_title') . " <$sender_email> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    return mail($receiver_email, $subject, $message, $headers);

}


if (!function_exists('checkCurrency')) {
    function checkCurrency($currency_code)
    {
        $currency = Currency::where('code', $currency_code)->first();
        if ($currency != null) {
            return true;
        }
        return null;
    }
}


if (!function_exists('showStatus')) {
    function showStatus($status)
    {
        if ($status == 1) {
            return 'Active';
        }
        return 'Inactive';
    }
}


if (!function_exists('permissionCheck')) {
    function permissionCheck($route_name)
    {
        if (auth()->check()) {
            if (auth()->user()->role_id == 1) {
                return TRUE;
            } else {

                if (isModuleActive('OrgInstructorPolicy')) {
                    if (auth()->user()->role_id == 2) {
                        $roles = app('policy_permission_list');
                        $role = $roles->where('id', auth()->user()->policy_id)->first();
                    } else {
                        $roles = app('permission_list');
                        $role = $roles->where('id', auth()->user()->role_id)->first();

                    }
                    if ($role != null && $role->permissions->contains('route', $route_name)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else {
                    $roles = app('permission_list');
                    $role = $roles->where('id', auth()->user()->role_id)->first();
                    if ($role != null && $role->permissions->contains('route', $route_name)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }

            }
        }
        return FALSE;
    }
}

//formats price to home default price with convertion
if (!function_exists('single_price')) {
    function single_price($price)
    {
        return getPriceFormat($price);
    }
}


//Messages
if (!function_exists('getConversations')) {
    function getConversations($messages)
    {
        $output = '';
        if ($messages) {
            foreach ($messages as $key => $message) {
                if ($message->sender_id == Auth::id()) {
                    $output .= '
                            <div class="single_message_chat">
                                <div class="message_pre_left">
                                    <div class="message_preview_thumb">
                                        <img src="' . url(@$message->sender->image) . '" alt="">
                                    </div>
                                    <div class="messges_info">
                                        <h4>' . @$message->sender->name . '</h4>
                                        <p>' . @$message->created_at . '</p>
                                    </div>
                                </div>
                                <div class="message_content_view red_border">
                                    <p>' . @$message->message . '</p>
                                </div>
                            </div>';
                } else {
                    $output .= '
                        <div class="single_message_chat sender_message">
                            <div class="message_pre_left">
                                <div class="messges_info">
                                <h4>' . @$message->sender->name . '</h4>
                                <p>' . @$message->created_at . '</p>
                                </div>
                                <div class="message_preview_thumb">
                                <img src="' . url(@$message->sender->image) . '" alt="">
                                </div>
                            </div>
                            <div class="message_content_view">
                                <p>' . @$message->message . '</p>
                            </div>
                        </div>';
                }
            }
            return $output;
        } else {
            $message = trans("communication.Let's say Hi");
            $output = '<p class="NoMessageFound">' . $message . '!</p>';
        }
        return $output;

    }
}


// checking module enable/disable
if (!function_exists('checkModuleEnable')) {
    function checkModuleEnable($module = null, $name = null)
    {
        if ($name) {
            return true;
        } else {
            return false;
        }

    }
}


if (!function_exists('getHeaderCategories')) {
    function getHeaderCategories()
    {
        return Category::with('subcategories')->where('status', 1)->orderBy('position_order', 'ASC')->get();
    }
}

/*
if (!function_exists('returnList')) {
version 4.7
    function returnList()
    {
        $list = [
            'fa-glass' => 'f000',
            'fa-music' => 'f001',
            'fa-search' => 'f002',
            'fa-envelope-o' => 'f003',
            'fa-heart' => 'f004',
            'fa-star' => 'f005',
            'fa-star-o' => 'f006',
            'fa-user' => 'f007',
            'fa-film' => 'f008',
            'fa-th-large' => 'f009',
            'fa-th' => 'f00a',
            'fa-th-list' => 'f00b',
            'fa-check' => 'f00c',
            'fa-times' => 'f00d',
            'fa-search-plus' => 'f00e',
            'fa-search-minus' => 'f010',
            'fa-power-off' => 'f011',
            'fa-signal' => 'f012',
            'fa-cog' => 'f013',
            'fa-trash-o' => 'f014',
            'fa-home' => 'f015',
            'fa-file-o' => 'f016',
            'fa-clock-o' => 'f017',
            'fa-road' => 'f018',
            'fa-download' => 'f019',
            'fa-arrow-circle-o-down' => 'f01a',
            'fa-arrow-circle-o-up' => 'f01b',
            'fa-inbox' => 'f01c',
            'fa-play-circle-o' => 'f01d',
            'fa-repeat' => 'f01e',
            'fa-refresh' => 'f021',
            'fa-list-alt' => 'f022',
            'fa-lock' => 'f023',
            'fa-flag' => 'f024',
            'fa-headphones' => 'f025',
            'fa-volume-off' => 'f026',
            'fa-volume-down' => 'f027',
            'fa-volume-up' => 'f028',
            'fa-qrcode' => 'f029',
            'fa-barcode' => 'f02a',
            'fa-tag' => 'f02b',
            'fa-tags' => 'f02c',
            'fa-book' => 'f02d',
            'fa-bookmark' => 'f02e',
            'fa-print' => 'f02f',
            'fa-camera' => 'f030',
            'fa-font' => 'f031',
            'fa-bold' => 'f032',
            'fa-italic' => 'f033',
            'fa-text-height' => 'f034',
            'fa-text-width' => 'f035',
            'fa-align-left' => 'f036',
            'fa-align-center' => 'f037',
            'fa-align-right' => 'f038',
            'fa-align-justify' => 'f039',
            'fa-list' => 'f03a',
            'fa-outdent' => 'f03b',
            'fa-indent' => 'f03c',
            'fa-video-camera' => 'f03d',
            'fa-picture-o' => 'f03e',
            'fa-pencil' => 'f040',
            'fa-map-marker' => 'f041',
            'fa-adjust' => 'f042',
            'fa-tint' => 'f043',
            'fa-pencil-square-o' => 'f044',
            'fa-share-square-o' => 'f045',
            'fa-check-square-o' => 'f046',
            'fa-arrows' => 'f047',
            'fa-step-backward' => 'f048',
            'fa-fast-backward' => 'f049',
            'fa-backward' => 'f04a',
            'fa-play' => 'f04b',
            'fa-pause' => 'f04c',
            'fa-stop' => 'f04d',
            'fa-forward' => 'f04e',
            'fa-fast-forward' => 'f050',
            'fa-step-forward' => 'f051',
            'fa-eject' => 'f052',
            'fa-chevron-left' => 'f053',
            'fa-chevron-right' => 'f054',
            'fa-plus-circle' => 'f055',
            'fa-minus-circle' => 'f056',
            'fa-times-circle' => 'f057',
            'fa-check-circle' => 'f058',
            'fa-question-circle' => 'f059',
            'fa-info-circle' => 'f05a',
            'fa-crosshairs' => 'f05b',
            'fa-times-circle-o' => 'f05c',
            'fa-check-circle-o' => 'f05d',
            'fa-ban' => 'f05e',
            'fa-arrow-left' => 'f060',
            'fa-arrow-right' => 'f061',
            'fa-arrow-up' => 'f062',
            'fa-arrow-down' => 'f063',
            'fa-share' => 'f064',
            'fa-expand' => 'f065',
            'fa-compress' => 'f066',
            'fa-plus' => 'f067',
            'fa-minus' => 'f068',
            'fa-asterisk' => 'f069',
            'fa-exclamation-circle' => 'f06a',
            'fa-gift' => 'f06b',
            'fa-leaf' => 'f06c',
            'fa-fire' => 'f06d',
            'fa-eye' => 'f06e',
            'fa-eye-slash' => 'f070',
            'fa-exclamation-triangle' => 'f071',
            'fa-plane' => 'f072',
            'fa-calendar' => 'f073',
            'fa-random' => 'f074',
            'fa-comment' => 'f075',
            'fa-magnet' => 'f076',
            'fa-chevron-up' => 'f077',
            'fa-chevron-down' => 'f078',
            'fa-retweet' => 'f079',
            'fa-shopping-cart' => 'f07a',
            'fa-folder' => 'f07b',
            'fa-folder-open' => 'f07c',
            'fa-arrows-v' => 'f07d',
            'fa-arrows-h' => 'f07e',
            'fa-bar-chart' => 'f080',
            'fa-twitter-square' => 'f081',
            'fa-facebook-square' => 'f082',
            'fa-camera-retro' => 'f083',
            'fa-key' => 'f084',
            'fa-cogs' => 'f085',
            'fa-comments' => 'f086',
            'fa-thumbs-o-up' => 'f087',
            'fa-thumbs-o-down' => 'f088',
            'fa-star-half' => 'f089',
            'fa-heart-o' => 'f08a',
            'fa-sign-out' => 'f08b',
            'fa-linkedin-square' => 'f08c',
            'fa-thumb-tack' => 'f08d',
            'fa-external-link' => 'f08e',
            'fa-sign-in' => 'f090',
            'fa-trophy' => 'f091',
            'fa-github-square' => 'f092',
            'fa-upload' => 'f093',
            'fa-lemon-o' => 'f094',
            'fa-phone' => 'f095',
            'fa-square-o' => 'f096',
            'fa-bookmark-o' => 'f097',
            'fa-phone-square' => 'f098',
            'fa-twitter' => 'f099',
            'fa-facebook' => 'f09a',
            'fa-github' => 'f09b',
            'fa-unlock' => 'f09c',
            'fa-credit-card' => 'f09d',
            'fa-rss' => 'f09e',
            'fa-hdd-o' => 'f0a0',
            'fa-bullhorn' => 'f0a1',
            'fa-bell' => 'f0f3',
            'fa-certificate' => 'f0a3',
            'fa-hand-o-right' => 'f0a4',
            'fa-hand-o-left' => 'f0a5',
            'fa-hand-o-up' => 'f0a6',
            'fa-hand-o-down' => 'f0a7',
            'fa-arrow-circle-left' => 'f0a8',
            'fa-arrow-circle-right' => 'f0a9',
            'fa-arrow-circle-up' => 'f0aa',
            'fa-arrow-circle-down' => 'f0ab',
            'fa-globe' => 'f0ac',
            'fa-wrench' => 'f0ad',
            'fa-tasks' => 'f0ae',
            'fa-filter' => 'f0b0',
            'fa-briefcase' => 'f0b1',
            'fa-arrows-alt' => 'f0b2',
            'fa-users' => 'f0c0',
            'fa-link' => 'f0c1',
            'fa-cloud' => 'f0c2',
            'fa-flask' => 'f0c3',
            'fa-scissors' => 'f0c4',
            'fa-files-o' => 'f0c5',
            'fa-paperclip' => 'f0c6',
            'fa-floppy-o' => 'f0c7',
            'fa-square' => 'f0c8',
            'fa-bars' => 'f0c9',
            'fa-list-ul' => 'f0ca',
            'fa-list-ol' => 'f0cb',
            'fa-strikethrough' => 'f0cc',
            'fa-underline' => 'f0cd',
            'fa-table' => 'f0ce',
            'fa-magic' => 'f0d0',
            'fa-truck' => 'f0d1',
            'fa-pinterest' => 'f0d2',
            'fa-pinterest-square' => 'f0d3',
            'fa-google-plus-square' => 'f0d4',
            'fa-google-plus' => 'f0d5',
            'fa-money' => 'f0d6',
            'fa-caret-down' => 'f0d7',
            'fa-caret-up' => 'f0d8',
            'fa-caret-left' => 'f0d9',
            'fa-caret-right' => 'f0da',
            'fa-columns' => 'f0db',
            'fa-sort' => 'f0dc',
            'fa-sort-desc' => 'f0dd',
            'fa-sort-asc' => 'f0de',
            'fa-envelope' => 'f0e0',
            'fa-linkedin' => 'f0e1',
            'fa-undo' => 'f0e2',
            'fa-gavel' => 'f0e3',
            'fa-tachometer' => 'f0e4',
            'fa-comment-o' => 'f0e5',
            'fa-comments-o' => 'f0e6',
            'fa-bolt' => 'f0e7',
            'fa-sitemap' => 'f0e8',
            'fa-umbrella' => 'f0e9',
            'fa-clipboard' => 'f0ea',
            'fa-lightbulb-o' => 'f0eb',
            'fa-exchange' => 'f0ec',
            'fa-cloud-download' => 'f0ed',
            'fa-cloud-upload' => 'f0ee',
            'fa-user-md' => 'f0f0',
            'fa-stethoscope' => 'f0f1',
            'fa-suitcase' => 'f0f2',
            'fa-bell-o' => 'f0a2',
            'fa-coffee' => 'f0f4',
            'fa-cutlery' => 'f0f5',
            'fa-file-text-o' => 'f0f6',
            'fa-building-o' => 'f0f7',
            'fa-hospital-o' => 'f0f8',
            'fa-ambulance' => 'f0f9',
            'fa-medkit' => 'f0fa',
            'fa-fighter-jet' => 'f0fb',
            'fa-beer' => 'f0fc',
            'fa-h-square' => 'f0fd',
            'fa-plus-square' => 'f0fe',
            'fa-angle-double-left' => 'f100',
            'fa-angle-double-right' => 'f101',
            'fa-angle-double-up' => 'f102',
            'fa-angle-double-down' => 'f103',
            'fa-angle-left' => 'f104',
            'fa-angle-right' => 'f105',
            'fa-angle-up' => 'f106',
            'fa-angle-down' => 'f107',
            'fa-desktop' => 'f108',
            'fa-laptop' => 'f109',
            'fa-tablet' => 'f10a',
            'fa-mobile' => 'f10b',
            'fa-circle-o' => 'f10c',
            'fa-quote-left' => 'f10d',
            'fa-quote-right' => 'f10e',
            'fa-spinner' => 'f110',
            'fa-circle' => 'f111',
            'fa-reply' => 'f112',
            'fa-github-alt' => 'f113',
            'fa-folder-o' => 'f114',
            'fa-folder-open-o' => 'f115',
            'fa-smile-o' => 'f118',
            'fa-frown-o' => 'f119',
            'fa-meh-o' => 'f11a',
            'fa-gamepad' => 'f11b',
            'fa-keyboard-o' => 'f11c',
            'fa-flag-o' => 'f11d',
            'fa-flag-checkered' => 'f11e',
            'fa-terminal' => 'f120',
            'fa-code' => 'f121',
            'fa-reply-all' => 'f122',
            'fa-star-half-o' => 'f123',
            'fa-location-arrow' => 'f124',
            'fa-crop' => 'f125',
            'fa-code-fork' => 'f126',
            'fa-chain-broken' => 'f127',
            'fa-question' => 'f128',
            'fa-info' => 'f129',
            'fa-exclamation' => 'f12a',
            'fa-superscript' => 'f12b',
            'fa-subscript' => 'f12c',
            'fa-eraser' => 'f12d',
            'fa-puzzle-piece' => 'f12e',
            'fa-microphone' => 'f130',
            'fa-microphone-slash' => 'f131',
            'fa-shield' => 'f132',
            'fa-calendar-o' => 'f133',
            'fa-fire-extinguisher' => 'f134',
            'fa-rocket' => 'f135',
            'fa-maxcdn' => 'f136',
            'fa-chevron-circle-left' => 'f137',
            'fa-chevron-circle-right' => 'f138',
            'fa-chevron-circle-up' => 'f139',
            'fa-chevron-circle-down' => 'f13a',
            'fa-html5' => 'f13b',
            'fa-css3' => 'f13c',
            'fa-anchor' => 'f13d',
            'fa-unlock-alt' => 'f13e',
            'fa-bullseye' => 'f140',
            'fa-ellipsis-h' => 'f141',
            'fa-ellipsis-v' => 'f142',
            'fa-rss-square' => 'f143',
            'fa-play-circle' => 'f144',
            'fa-ticket' => 'f145',
            'fa-minus-square' => 'f146',
            'fa-minus-square-o' => 'f147',
            'fa-level-up' => 'f148',
            'fa-level-down' => 'f149',
            'fa-check-square' => 'f14a',
            'fa-pencil-square' => 'f14b',
            'fa-external-link-square' => 'f14c',
            'fa-share-square' => 'f14d',
            'fa-compass' => 'f14e',
            'fa-caret-square-o-down' => 'f150',
            'fa-caret-square-o-up' => 'f151',
            'fa-caret-square-o-right' => 'f152',
            'fa-eur' => 'f153',
            'fa-gbp' => 'f154',
            'fa-usd' => 'f155',
            'fa-inr' => 'f156',
            'fa-jpy' => 'f157',
            'fa-rub' => 'f158',
            'fa-krw' => 'f159',
            'fa-btc' => 'f15a',
            'fa-file' => 'f15b',
            'fa-file-text' => 'f15c',
            'fa-sort-alpha-asc' => 'f15d',
            'fa-sort-alpha-desc' => 'f15e',
            'fa-sort-amount-asc' => 'f160',
            'fa-sort-amount-desc' => 'f161',
            'fa-sort-numeric-asc' => 'f162',
            'fa-sort-numeric-desc' => 'f163',
            'fa-thumbs-up' => 'f164',
            'fa-thumbs-down' => 'f165',
            'fa-youtube-square' => 'f166',
            'fa-youtube' => 'f167',
            'fa-xing' => 'f168',
            'fa-xing-square' => 'f169',
            'fa-youtube-play' => 'f16a',
            'fa-dropbox' => 'f16b',
            'fa-stack-overflow' => 'f16c',
            'fa-instagram' => 'f16d',
            'fa-flickr' => 'f16e',
            'fa-adn' => 'f170',
            'fa-bitbucket' => 'f171',
            'fa-bitbucket-square' => 'f172',
            'fa-tumblr' => 'f173',
            'fa-tumblr-square' => 'f174',
            'fa-long-arrow-down' => 'f175',
            'fa-long-arrow-up' => 'f176',
            'fa-long-arrow-left' => 'f177',
            'fa-long-arrow-right' => 'f178',
            'fa-apple' => 'f179',
            'fa-windows' => 'f17a',
            'fa-android' => 'f17b',
            'fa-linux' => 'f17c',
            'fa-dribbble' => 'f17d',
            'fa-skype' => 'f17e',
            'fa-foursquare' => 'f180',
            'fa-trello' => 'f181',
            'fa-female' => 'f182',
            'fa-male' => 'f183',
            'fa-gratipay' => 'f184',
            'fa-sun-o' => 'f185',
            'fa-moon-o' => 'f186',
            'fa-archive' => 'f187',
            'fa-bug' => 'f188',
            'fa-vk' => 'f189',
            'fa-weibo' => 'f18a',
            'fa-renren' => 'f18b',
            'fa-pagelines' => 'f18c',
            'fa-stack-exchange' => 'f18d',
            'fa-arrow-circle-o-right' => 'f18e',
            'fa-arrow-circle-o-left' => 'f190',
            'fa-caret-square-o-left' => 'f191',
            'fa-dot-circle-o' => 'f192',
            'fa-wheelchair' => 'f193',
            'fa-vimeo-square' => 'f194',
            'fa-try' => 'f195',
            'fa-plus-square-o' => 'f196',
            'fa-space-shuttle' => 'f197',
            'fa-slack' => 'f198',
            'fa-envelope-square' => 'f199',
            'fa-wordpress' => 'f19a',
            'fa-openid' => 'f19b',
            'fa-university' => 'f19c',
            'fa-graduation-cap' => 'f19d',
            'fa-yahoo' => 'f19e',
            'fa-google' => 'f1a0',
            'fa-reddit' => 'f1a1',
            'fa-reddit-square' => 'f1a2',
            'fa-stumbleupon-circle' => 'f1a3',
            'fa-stumbleupon' => 'f1a4',
            'fa-delicious' => 'f1a5',
            'fa-digg' => 'f1a6',
            'fa-pied-piper-pp' => 'f1a7',
            'fa-pied-piper-alt' => 'f1a8',
            'fa-drupal' => 'f1a9',
            'fa-joomla' => 'f1aa',
            'fa-language' => 'f1ab',
            'fa-fax' => 'f1ac',
            'fa-building' => 'f1ad',
            'fa-child' => 'f1ae',
            'fa-paw' => 'f1b0',
            'fa-spoon' => 'f1b1',
            'fa-cube' => 'f1b2',
            'fa-cubes' => 'f1b3',
            'fa-behance' => 'f1b4',
            'fa-behance-square' => 'f1b5',
            'fa-steam' => 'f1b6',
            'fa-steam-square' => 'f1b7',
            'fa-recycle' => 'f1b8',
            'fa-car' => 'f1b9',
            'fa-taxi' => 'f1ba',
            'fa-tree' => 'f1bb',
            'fa-spotify' => 'f1bc',
            'fa-deviantart' => 'f1bd',
            'fa-soundcloud' => 'f1be',
            'fa-database' => 'f1c0',
            'fa-file-pdf-o' => 'f1c1',
            'fa-file-word-o' => 'f1c2',
            'fa-file-excel-o' => 'f1c3',
            'fa-file-powerpoint-o' => 'f1c4',
            'fa-file-image-o' => 'f1c5',
            'fa-file-archive-o' => 'f1c6',
            'fa-file-audio-o' => 'f1c7',
            'fa-file-video-o' => 'f1c8',
            'fa-file-code-o' => 'f1c9',
            'fa-vine' => 'f1ca',
            'fa-codepen' => 'f1cb',
            'fa-jsfiddle' => 'f1cc',
            'fa-life-ring' => 'f1cd',
            'fa-circle-o-notch' => 'f1ce',
            'fa-rebel' => 'f1d0',
            'fa-empire' => 'f1d1',
            'fa-git-square' => 'f1d2',
            'fa-git' => 'f1d3',
            'fa-hacker-news' => 'f1d4',
            'fa-tencent-weibo' => 'f1d5',
            'fa-qq' => 'f1d6',
            'fa-weixin' => 'f1d7',
            'fa-paper-plane' => 'f1d8',
            'fa-paper-plane-o' => 'f1d9',
            'fa-history' => 'f1da',
            'fa-circle-thin' => 'f1db',
            'fa-header' => 'f1dc',
            'fa-paragraph' => 'f1dd',
            'fa-sliders' => 'f1de',
            'fa-share-alt' => 'f1e0',
            'fa-share-alt-square' => 'f1e1',
            'fa-bomb' => 'f1e2',
            'fa-futbol-o' => 'f1e3',
            'fa-tty' => 'f1e4',
            'fa-binoculars' => 'f1e5',
            'fa-plug' => 'f1e6',
            'fa-slideshare' => 'f1e7',
            'fa-twitch' => 'f1e8',
            'fa-yelp' => 'f1e9',
            'fa-newspaper-o' => 'f1ea',
            'fa-wifi' => 'f1eb',
            'fa-calculator' => 'f1ec',
            'fa-paypal' => 'f1ed',
            'fa-google-wallet' => 'f1ee',
            'fa-cc-visa' => 'f1f0',
            'fa-cc-mastercard' => 'f1f1',
            'fa-cc-discover' => 'f1f2',
            'fa-cc-amex' => 'f1f3',
            'fa-cc-paypal' => 'f1f4',
            'fa-cc-stripe' => 'f1f5',
            'fa-bell-slash' => 'f1f6',
            'fa-bell-slash-o' => 'f1f7',
            'fa-trash' => 'f1f8',
            'fa-copyright' => 'f1f9',
            'fa-at' => 'f1fa',
            'fa-eyedropper' => 'f1fb',
            'fa-paint-brush' => 'f1fc',
            'fa-birthday-cake' => 'f1fd',
            'fa-area-chart' => 'f1fe',
            'fa-pie-chart' => 'f200',
            'fa-line-chart' => 'f201',
            'fa-lastfm' => 'f202',
            'fa-lastfm-square' => 'f203',
            'fa-toggle-off' => 'f204',
            'fa-toggle-on' => 'f205',
            'fa-bicycle' => 'f206',
            'fa-bus' => 'f207',
            'fa-ioxhost' => 'f208',
            'fa-angellist' => 'f209',
            'fa-cc' => 'f20a',
            'fa-ils' => 'f20b',
            'fa-meanpath' => 'f20c',
            'fa-buysellads' => 'f20d',
            'fa-connectdevelop' => 'f20e',
            'fa-dashcube' => 'f210',
            'fa-forumbee' => 'f211',
            'fa-leanpub' => 'f212',
            'fa-sellsy' => 'f213',
            'fa-shirtsinbulk' => 'f214',
            'fa-simplybuilt' => 'f215',
            'fa-skyatlas' => 'f216',
            'fa-cart-plus' => 'f217',
            'fa-cart-arrow-down' => 'f218',
            'fa-diamond' => 'f219',
            'fa-ship' => 'f21a',
            'fa-user-secret' => 'f21b',
            'fa-motorcycle' => 'f21c',
            'fa-street-view' => 'f21d',
            'fa-heartbeat' => 'f21e',
            'fa-venus' => 'f221',
            'fa-mars' => 'f222',
            'fa-mercury' => 'f223',
            'fa-transgender' => 'f224',
            'fa-transgender-alt' => 'f225',
            'fa-venus-double' => 'f226',
            'fa-mars-double' => 'f227',
            'fa-venus-mars' => 'f228',
            'fa-mars-stroke' => 'f229',
            'fa-mars-stroke-v' => 'f22a',
            'fa-mars-stroke-h' => 'f22b',
            'fa-neuter' => 'f22c',
            'fa-genderless' => 'f22d',
            'fa-facebook-official' => 'f230',
            'fa-pinterest-p' => 'f231',
            'fa-whatsapp' => 'f232',
            'fa-server' => 'f233',
            'fa-user-plus' => 'f234',
            'fa-user-times' => 'f235',
            'fa-bed' => 'f236',
            'fa-viacoin' => 'f237',
            'fa-train' => 'f238',
            'fa-subway' => 'f239',
            'fa-medium' => 'f23a',
            'fa-y-combinator' => 'f23b',
            'fa-optin-monster' => 'f23c',
            'fa-opencart' => 'f23d',
            'fa-expeditedssl' => 'f23e',
            'fa-battery-full' => 'f240',
            'fa-battery-three-quarters' => 'f241',
            'fa-battery-half' => 'f242',
            'fa-battery-quarter' => 'f243',
            'fa-battery-empty' => 'f244',
            'fa-mouse-pointer' => 'f245',
            'fa-i-cursor' => 'f246',
            'fa-object-group' => 'f247',
            'fa-object-ungroup' => 'f248',
            'fa-sticky-note' => 'f249',
            'fa-sticky-note-o' => 'f24a',
            'fa-cc-jcb' => 'f24b',
            'fa-cc-diners-club' => 'f24c',
            'fa-clone' => 'f24d',
            'fa-balance-scale' => 'f24e',
            'fa-hourglass-o' => 'f250',
            'fa-hourglass-start' => 'f251',
            'fa-hourglass-half' => 'f252',
            'fa-hourglass-end' => 'f253',
            'fa-hourglass' => 'f254',
            'fa-hand-rock-o' => 'f255',
            'fa-hand-paper-o' => 'f256',
            'fa-hand-scissors-o' => 'f257',
            'fa-hand-lizard-o' => 'f258',
            'fa-hand-spock-o' => 'f259',
            'fa-hand-pointer-o' => 'f25a',
            'fa-hand-peace-o' => 'f25b',
            'fa-trademark' => 'f25c',
            'fa-registered' => 'f25d',
            'fa-creative-commons' => 'f25e',
            'fa-gg' => 'f260',
            'fa-gg-circle' => 'f261',
            'fa-tripadvisor' => 'f262',
            'fa-odnoklassniki' => 'f263',
            'fa-odnoklassniki-square' => 'f264',
            'fa-get-pocket' => 'f265',
            'fa-wikipedia-w' => 'f266',
            'fa-safari' => 'f267',
            'fa-chrome' => 'f268',
            'fa-firefox' => 'f269',
            'fa-opera' => 'f26a',
            'fa-internet-explorer' => 'f26b',
            'fa-television' => 'f26c',
            'fa-contao' => 'f26d',
            'fa-500px' => 'f26e',
            'fa-amazon' => 'f270',
            'fa-calendar-plus-o' => 'f271',
            'fa-calendar-minus-o' => 'f272',
            'fa-calendar-times-o' => 'f273',
            'fa-calendar-check-o' => 'f274',
            'fa-industry' => 'f275',
            'fa-map-pin' => 'f276',
            'fa-map-signs' => 'f277',
            'fa-map-o' => 'f278',
            'fa-map' => 'f279',
            'fa-commenting' => 'f27a',
            'fa-commenting-o' => 'f27b',
            'fa-houzz' => 'f27c',
            'fa-vimeo' => 'f27d',
            'fa-black-tie' => 'f27e',
            'fa-fonticons' => 'f280',
            'fa-reddit-alien' => 'f281',
            'fa-edge' => 'f282',
            'fa-credit-card-alt' => 'f283',
            'fa-codiepie' => 'f284',
            'fa-modx' => 'f285',
            'fa-fort-awesome' => 'f286',
            'fa-usb' => 'f287',
            'fa-product-hunt' => 'f288',
            'fa-mixcloud' => 'f289',
            'fa-scribd' => 'f28a',
            'fa-pause-circle' => 'f28b',
            'fa-pause-circle-o' => 'f28c',
            'fa-stop-circle' => 'f28d',
            'fa-stop-circle-o' => 'f28e',
            'fa-shopping-bag' => 'f290',
            'fa-shopping-basket' => 'f291',
            'fa-hashtag' => 'f292',
            'fa-bluetooth' => 'f293',
            'fa-bluetooth-b' => 'f294',
            'fa-percent' => 'f295',
            'fa-gitlab' => 'f296',
            'fa-wpbeginner' => 'f297',
            'fa-wpforms' => 'f298',
            'fa-envira' => 'f299',
            'fa-universal-access' => 'f29a',
            'fa-wheelchair-alt' => 'f29b',
            'fa-question-circle-o' => 'f29c',
            'fa-blind' => 'f29d',
            'fa-audio-description' => 'f29e',
            'fa-volume-control-phone' => 'f2a0',
            'fa-braille' => 'f2a1',
            'fa-assistive-listening-systems' => 'f2a2',
            'fa-american-sign-language-interpreting' => 'f2a3',
            'fa-deaf' => 'f2a4',
            'fa-glide' => 'f2a5',
            'fa-glide-g' => 'f2a6',
            'fa-sign-language' => 'f2a7',
            'fa-low-vision' => 'f2a8',
            'fa-viadeo' => 'f2a9',
            'fa-viadeo-square' => 'f2aa',
            'fa-snapchat' => 'f2ab',
            'fa-snapchat-ghost' => 'f2ac',
            'fa-snapchat-square' => 'f2ad',
            'fa-pied-piper' => 'f2ae',
            'fa-first-order' => 'f2b0',
            'fa-yoast' => 'f2b1',
            'fa-themeisle' => 'f2b2',
            'fa-google-plus-official' => 'f2b3',
            'fa-font-awesome' => 'f2b4'
        ];
        $str = '';
        foreach ($list as $class => $value) {
            $str .= '<option value="fa ' . $class . '"><i class="fa ' . $class . '"></i> ' . $class . ' </option>';
        }
        return $str;
    }
}*/

if (!function_exists('returnList')) {
    function returnList()
    {

        //version 5
        $list = [
            "fab fa-500px",
            "fab fa-accessible-icon",
            "fab fa-accusoft",
            "fas fa-address-book",
            "far fa-address-book",
            "fas fa-address-card",
            "far fa-address-card",
            "fas fa-adjust",
            "fab fa-adn",
            "fab fa-adversal",
            "fab fa-affiliatetheme",
            "fab fa-algolia",
            "fas fa-align-center",
            "fas fa-align-justify",
            "fas fa-align-left",
            "fas fa-align-right",
            "fab fa-amazon",
            "fas fa-ambulance",
            "fas fa-american-sign-language-interpreting",
            "fab fa-amilia",
            "fas fa-anchor",
            "fab fa-android",
            "fab fa-angellist",
            "fas fa-angle-double-down",
            "fas fa-angle-double-left",
            "fas fa-angle-double-right",
            "fas fa-angle-double-up",
            "fas fa-angle-down",
            "fas fa-angle-left",
            "fas fa-angle-right",
            "fas fa-angle-up",
            "fab fa-angrycreative",
            "fab fa-angular",
            "fab fa-app-store",
            "fab fa-app-store-ios",
            "fab fa-apper",
            "fab fa-apple",
            "fab fa-apple-pay",
            "fas fa-archive",
            "fas fa-arrow-alt-circle-down",
            "far fa-arrow-alt-circle-down",
            "fas fa-arrow-alt-circle-left",
            "far fa-arrow-alt-circle-left",
            "fas fa-arrow-alt-circle-right",
            "far fa-arrow-alt-circle-right",
            "fas fa-arrow-alt-circle-up",
            "far fa-arrow-alt-circle-up",
            "fas fa-arrow-circle-down",
            "fas fa-arrow-circle-left",
            "fas fa-arrow-circle-right",
            "fas fa-arrow-circle-up",
            "fas fa-arrow-down",
            "fas fa-arrow-left",
            "fas fa-arrow-right",
            "fas fa-arrow-up",
            "fas fa-arrows-alt",
            "fas fa-arrows-alt-h",
            "fas fa-arrows-alt-v",
            "fas fa-assistive-listening-systems",
            "fas fa-asterisk",
            "fab fa-asymmetrik",
            "fas fa-at",
            "fab fa-audible",
            "fas fa-audio-description",
            "fab fa-autoprefixer",
            "fab fa-avianex",
            "fab fa-aviato",
            "fab fa-aws",
            "fas fa-backward",
            "fas fa-balance-scale",
            "fas fa-ban",
            "fab fa-bandcamp",
            "fas fa-barcode",
            "fas fa-bars",
            "fas fa-bath",
            "fas fa-battery-empty",
            "fas fa-battery-full",
            "fas fa-battery-half",
            "fas fa-battery-quarter",
            "fas fa-battery-three-quarters",
            "fas fa-bed",
            "fas fa-beer",
            "fab fa-behance",
            "fab fa-behance-square",
            "fas fa-bell",
            "far fa-bell",
            "fas fa-bell-slash",
            "far fa-bell-slash",
            "fas fa-bicycle",
            "fab fa-bimobject",
            "fas fa-binoculars",
            "fas fa-birthday-cake",
            "fab fa-bitbucket",
            "fab fa-bitcoin",
            "fab fa-bity",
            "fab fa-black-tie",
            "fab fa-blackberry",
            "fas fa-blind",
            "fab fa-blogger",
            "fab fa-blogger-b",
            "fab fa-bluetooth",
            "fab fa-bluetooth-b",
            "fas fa-bold",
            "fas fa-bolt",
            "fas fa-bomb",
            "fas fa-book",
            "fas fa-bookmark",
            "far fa-bookmark",
            "fas fa-braille",
            "fas fa-briefcase",
            "fab fa-btc",
            "fas fa-bug",
            "fas fa-building",
            "far fa-building",
            "fas fa-bullhorn",
            "fas fa-bullseye",
            "fab fa-buromobelexperte",
            "fas fa-bus",
            "fab fa-buysellads",
            "fas fa-calculator",
            "fas fa-calendar",
            "far fa-calendar",
            "fas fa-calendar-alt",
            "far fa-calendar-alt",
            "fas fa-calendar-check",
            "far fa-calendar-check",
            "fas fa-calendar-minus",
            "far fa-calendar-minus",
            "fas fa-calendar-plus",
            "far fa-calendar-plus",
            "fas fa-calendar-times",
            "far fa-calendar-times",
            "fas fa-camera",
            "fas fa-camera-retro",
            "fas fa-car",
            "fas fa-caret-down",
            "fas fa-caret-left",
            "fas fa-caret-right",
            "fas fa-caret-square-down",
            "far fa-caret-square-down",
            "fas fa-caret-square-left",
            "far fa-caret-square-left",
            "fas fa-caret-square-right",
            "far fa-caret-square-right",
            "fas fa-caret-square-up",
            "far fa-caret-square-up",
            "fas fa-caret-up",
            "fas fa-cart-arrow-down",
            "fas fa-cart-plus",
            "fab fa-cc-amex",
            "fab fa-cc-apple-pay",
            "fab fa-cc-diners-club",
            "fab fa-cc-discover",
            "fab fa-cc-jcb",
            "fab fa-cc-mastercard",
            "fab fa-cc-paypal",
            "fab fa-cc-stripe",
            "fab fa-cc-visa",
            "fab fa-centercode",
            "fas fa-certificate",
            "fas fa-chart-area",
            "fas fa-chart-bar",
            "far fa-chart-bar",
            "fas fa-chart-line",
            "fas fa-chart-pie",
            "fas fa-check",
            "fas fa-check-circle",
            "far fa-check-circle",
            "fas fa-check-square",
            "far fa-check-square",
            "fas fa-chevron-circle-down",
            "fas fa-chevron-circle-left",
            "fas fa-chevron-circle-right",
            "fas fa-chevron-circle-up",
            "fas fa-chevron-down",
            "fas fa-chevron-left",
            "fas fa-chevron-right",
            "fas fa-chevron-up",
            "fas fa-child",
            "fab fa-chrome",
            "fas fa-circle",
            "far fa-circle",
            "fas fa-circle-notch",
            "fas fa-clipboard",
            "far fa-clipboard",
            "fas fa-clock",
            "far fa-clock",
            "fas fa-clone",
            "far fa-clone",
            "fas fa-closed-captioning",
            "far fa-closed-captioning",
            "fas fa-cloud",
            "fas fa-cloud-download-alt",
            "fas fa-cloud-upload-alt",
            "fab fa-cloudscale",
            "fab fa-cloudsmith",
            "fab fa-cloudversify",
            "fas fa-code",
            "fas fa-code-branch",
            "fab fa-codepen",
            "fab fa-codiepie",
            "fas fa-coffee",
            "fas fa-cog",
            "fas fa-cogs",
            "fas fa-columns",
            "fas fa-comment",
            "far fa-comment",
            "fas fa-comment-alt",
            "far fa-comment-alt",
            "fas fa-comments",
            "far fa-comments",
            "fas fa-compass",
            "far fa-compass",
            "fas fa-compress",
            "fab fa-connectdevelop",
            "fab fa-contao",
            "fas fa-copy",
            "far fa-copy",
            "fas fa-copyright",
            "far fa-copyright",
            "fab fa-cpanel",
            "fab fa-creative-commons",
            "fas fa-credit-card",
            "far fa-credit-card",
            "fas fa-crop",
            "fas fa-crosshairs",
            "fab fa-css3",
            "fab fa-css3-alt",
            "fas fa-cube",
            "fas fa-cubes",
            "fas fa-cut",
            "fab fa-cuttlefish",
            "fab fa-d-and-d",
            "fab fa-dashcube",
            "fas fa-database",
            "fas fa-deaf",
            "fab fa-delicious",
            "fab fa-deploydog",
            "fab fa-deskpro",
            "fas fa-desktop",
            "fab fa-deviantart",
            "fab fa-digg",
            "fab fa-digital-ocean",
            "fab fa-discord",
            "fab fa-discourse",
            "fab fa-dochub",
            "fab fa-docker",
            "fas fa-dollar-sign",
            "fas fa-dot-circle",
            "far fa-dot-circle",
            "fas fa-download",
            "fab fa-draft2digital",
            "fab fa-dribbble",
            "fab fa-dribbble-square",
            "fab fa-dropbox",
            "fab fa-drupal",
            "fab fa-dyalog",
            "fab fa-earlybirds",
            "fab fa-edge",
            "fas fa-edit",
            "far fa-edit",
            "fas fa-eject",
            "fas fa-ellipsis-h",
            "fas fa-ellipsis-v",
            "fab fa-ember",
            "fab fa-empire",
            "fas fa-envelope",
            "far fa-envelope",
            "fas fa-envelope-open",
            "far fa-envelope-open",
            "fas fa-envelope-square",
            "fab fa-envira",
            "fas fa-eraser",
            "fab fa-erlang",
            "fab fa-etsy",
            "fas fa-euro-sign",
            "fas fa-exchange-alt",
            "fas fa-exclamation",
            "fas fa-exclamation-circle",
            "fas fa-exclamation-triangle",
            "fas fa-expand",
            "fas fa-expand-arrows-alt",
            "fab fa-expeditedssl",
            "fas fa-external-link-alt",
            "fas fa-external-link-square-alt",
            "fas fa-eye",
            "fas fa-eye-dropper",
            "fas fa-eye-slash",
            "far fa-eye-slash",
            "fab fa-facebook",
            "fab fa-facebook-f",
            "fab fa-facebook-messenger",
            "fab fa-facebook-square",
            "fas fa-fast-backward",
            "fas fa-fast-forward",
            "fas fa-fax",
            "fas fa-female",
            "fas fa-fighter-jet",
            "fas fa-file",
            "far fa-file",
            "fas fa-file-alt",
            "far fa-file-alt",
            "fas fa-file-archive",
            "far fa-file-archive",
            "fas fa-file-audio",
            "far fa-file-audio",
            "fas fa-file-code",
            "far fa-file-code",
            "fas fa-file-excel",
            "far fa-file-excel",
            "fas fa-file-image",
            "far fa-file-image",
            "fas fa-file-pdf",
            "far fa-file-pdf",
            "fas fa-file-powerpoint",
            "far fa-file-powerpoint",
            "fas fa-file-video",
            "far fa-file-video",
            "fas fa-file-word",
            "far fa-file-word",
            "fas fa-film",
            "fas fa-filter",
            "fas fa-fire",
            "fas fa-fire-extinguisher",
            "fab fa-firefox",
            "fab fa-first-order",
            "fab fa-firstdraft",
            "fas fa-flag",
            "far fa-flag",
            "fas fa-flag-checkered",
            "fas fa-flask",
            "fab fa-flickr",
            "fab fa-fly",
            "fas fa-folder",
            "far fa-folder",
            "fas fa-folder-open",
            "far fa-folder-open",
            "fas fa-font",
            "fab fa-font-awesome",
            "fab fa-font-awesome-alt",
            "fab fa-font-awesome-flag",
            "fab fa-fonticons",
            "fab fa-fonticons-fi",
            "fab fa-fort-awesome",
            "fab fa-fort-awesome-alt",
            "fab fa-forumbee",
            "fas fa-forward",
            "fab fa-foursquare",
            "fab fa-free-code-camp",
            "fab fa-freebsd",
            "fas fa-frown",
            "far fa-frown",
            "fas fa-futbol",
            "far fa-futbol",
            "fas fa-gamepad",
            "fas fa-gavel",
            "fas fa-gem",
            "far fa-gem",
            "fas fa-genderless",
            "fab fa-get-pocket",
            "fab fa-gg",
            "fab fa-gg-circle",
            "fas fa-gift",
            "fab fa-git",
            "fab fa-git-square",
            "fab fa-github",
            "fab fa-github-alt",
            "fab fa-github-square",
            "fab fa-gitkraken",
            "fab fa-gitlab",
            "fab fa-gitter",
            "fas fa-glass-martini",
            "fab fa-glide",
            "fab fa-glide-g",
            "fas fa-globe",
            "fab fa-gofore",
            "fab fa-goodreads",
            "fab fa-goodreads-g",
            "fab fa-google",
            "fab fa-google-drive",
            "fab fa-google-play",
            "fab fa-google-plus",
            "fab fa-google-plus-g",
            "fab fa-google-plus-square",
            "fab fa-google-wallet",
            "fas fa-graduation-cap",
            "fab fa-gratipay",
            "fab fa-grav",
            "fab fa-gripfire",
            "fab fa-grunt",
            "fab fa-gulp",
            "fas fa-h-square",
            "fab fa-hacker-news",
            "fab fa-hacker-news-square",
            "fas fa-hand-lizard",
            "far fa-hand-lizard",
            "fas fa-hand-paper",
            "far fa-hand-paper",
            "fas fa-hand-peace",
            "far fa-hand-peace",
            "fas fa-hand-point-down",
            "far fa-hand-point-down",
            "fas fa-hand-point-left",
            "far fa-hand-point-left",
            "fas fa-hand-point-right",
            "far fa-hand-point-right",
            "fas fa-hand-point-up",
            "far fa-hand-point-up",
            "fas fa-hand-pointer",
            "far fa-hand-pointer",
            "fas fa-hand-rock",
            "far fa-hand-rock",
            "fas fa-hand-scissors",
            "far fa-hand-scissors",
            "fas fa-hand-spock",
            "far fa-hand-spock",
            "fas fa-handshake",
            "far fa-handshake",
            "fas fa-hashtag",
            "fas fa-hdd",
            "far fa-hdd",
            "fas fa-heading",
            "fas fa-headphones",
            "fas fa-heart",
            "far fa-heart",
            "fas fa-heartbeat",
            "fab fa-hire-a-helper",
            "fas fa-history",
            "fas fa-home",
            "fab fa-hooli",
            "fas fa-hospital",
            "far fa-hospital",
            "fab fa-hotjar",
            "fas fa-hourglass",
            "far fa-hourglass",
            "fas fa-hourglass-end",
            "fas fa-hourglass-half",
            "fas fa-hourglass-start",
            "fab fa-houzz",
            "fab fa-html5",
            "fab fa-hubspot",
            "fas fa-i-cursor",
            "fas fa-id-badge",
            "far fa-id-badge",
            "fas fa-id-card",
            "far fa-id-card",
            "fas fa-image",
            "far fa-image",
            "fas fa-images",
            "far fa-images",
            "fab fa-imdb",
            "fas fa-inbox",
            "fas fa-indent",
            "fas fa-industry",
            "fas fa-info",
            "fas fa-info-circle",
            "fab fa-instagram",
            "fab fa-internet-explorer",
            "fab fa-ioxhost",
            "fas fa-italic",
            "fab fa-itunes",
            "fab fa-itunes-note",
            "fab fa-jenkins",
            "fab fa-joget",
            "fab fa-joomla",
            "fab fa-js",
            "fab fa-js-square",
            "fab fa-jsfiddle",
            "fas fa-key",
            "fas fa-keyboard",
            "far fa-keyboard",
            "fab fa-keycdn",
            "fab fa-kickstarter",
            "fab fa-kickstarter-k",
            "fas fa-language",
            "fas fa-laptop",
            "fab fa-laravel",
            "fab fa-lastfm",
            "fab fa-lastfm-square",
            "fas fa-leaf",
            "fab fa-leanpub",
            "fas fa-lemon",
            "far fa-lemon",
            "fab fa-less",
            "fas fa-level-down-alt",
            "fas fa-level-up-alt",
            "fas fa-life-ring",
            "far fa-life-ring",
            "fas fa-lightbulb",
            "far fa-lightbulb",
            "fab fa-line",
            "fas fa-link",
            "fab fa-linkedin",
            "fab fa-linkedin-in",
            "fab fa-linode",
            "fab fa-linux",
            "fas fa-lira-sign",
            "fas fa-list",
            "fas fa-list-alt",
            "far fa-list-alt",
            "fas fa-list-ol",
            "fas fa-list-ul",
            "fas fa-location-arrow",
            "fas fa-lock",
            "fas fa-lock-open",
            "fas fa-long-arrow-alt-down",
            "fas fa-long-arrow-alt-left",
            "fas fa-long-arrow-alt-right",
            "fas fa-long-arrow-alt-up",
            "fas fa-low-vision",
            "fab fa-lyft",
            "fab fa-magento",
            "fas fa-magic",
            "fas fa-magnet",
            "fas fa-male",
            "fas fa-map",
            "far fa-map",
            "fas fa-map-marker",
            "fas fa-map-marker-alt",
            "fas fa-map-pin",
            "fas fa-map-signs",
            "fas fa-mars",
            "fas fa-mars-double",
            "fas fa-mars-stroke",
            "fas fa-mars-stroke-h",
            "fas fa-mars-stroke-v",
            "fab fa-maxcdn",
            "fab fa-medapps",
            "fab fa-medium",
            "fab fa-medium-m",
            "fas fa-medkit",
            "fab fa-medrt",
            "fab fa-meetup",
            "fas fa-meh",
            "far fa-meh",
            "fas fa-mercury",
            "fas fa-microchip",
            "fas fa-microphone",
            "fas fa-microphone-slash",
            "fab fa-microsoft",
            "fas fa-minus",
            "fas fa-minus-circle",
            "fas fa-minus-square",
            "far fa-minus-square",
            "fab fa-mix",
            "fab fa-mixcloud",
            "fab fa-mizuni",
            "fas fa-mobile",
            "fas fa-mobile-alt",
            "fab fa-modx",
            "fab fa-monero",
            "fas fa-money-bill-alt",
            "far fa-money-bill-alt",
            "fas fa-moon",
            "far fa-moon",
            "fas fa-motorcycle",
            "fas fa-mouse-pointer",
            "fas fa-music",
            "fab fa-napster",
            "fas fa-neuter",
            "fas fa-newspaper",
            "far fa-newspaper",
            "fab fa-nintendo-switch",
            "fab fa-node",
            "fab fa-node-js",
            "fab fa-npm",
            "fab fa-ns8",
            "fab fa-nutritionix",
            "fas fa-object-group",
            "far fa-object-group",
            "fas fa-object-ungroup",
            "far fa-object-ungroup",
            "fab fa-odnoklassniki",
            "fab fa-odnoklassniki-square",
            "fab fa-opencart",
            "fab fa-openid",
            "fab fa-opera",
            "fab fa-optin-monster",
            "fab fa-osi",
            "fas fa-outdent",
            "fab fa-page4",
            "fab fa-pagelines",
            "fas fa-paint-brush",
            "fab fa-palfed",
            "fas fa-paper-plane",
            "far fa-paper-plane",
            "fas fa-paperclip",
            "fas fa-paragraph",
            "fas fa-paste",
            "fab fa-patreon",
            "fas fa-pause",
            "fas fa-pause-circle",
            "far fa-pause-circle",
            "fas fa-paw",
            "fab fa-paypal",
            "fas fa-pen-square",
            "fas fa-pencil-alt",
            "fas fa-percent",
            "fab fa-periscope",
            "fab fa-phabricator",
            "fab fa-phoenix-framework",
            "fas fa-phone",
            "fas fa-phone-square",
            "fas fa-phone-volume",
            "fab fa-pied-piper",
            "fab fa-pied-piper-alt",
            "fab fa-pied-piper-pp",
            "fab fa-pinterest",
            "fab fa-pinterest-p",
            "fab fa-pinterest-square",
            "fas fa-plane",
            "fas fa-play",
            "fas fa-play-circle",
            "far fa-play-circle",
            "fab fa-playstation",
            "fas fa-plug",
            "fas fa-plus",
            "fas fa-plus-circle",
            "fas fa-plus-square",
            "far fa-plus-square",
            "fas fa-podcast",
            "fas fa-pound-sign",
            "fas fa-power-off",
            "fas fa-print",
            "fab fa-product-hunt",
            "fab fa-pushed",
            "fas fa-puzzle-piece",
            "fab fa-python",
            "fab fa-qq",
            "fas fa-qrcode",
            "fas fa-question",
            "fas fa-question-circle",
            "far fa-question-circle",
            "fab fa-quora",
            "fas fa-quote-left",
            "fas fa-quote-right",
            "fas fa-random",
            "fab fa-ravelry",
            "fab fa-react",
            "fab fa-rebel",
            "fas fa-recycle",
            "fab fa-red-river",
            "fab fa-reddit",
            "fab fa-reddit-alien",
            "fab fa-reddit-square",
            "fas fa-redo",
            "fas fa-redo-alt",
            "fas fa-registered",
            "far fa-registered",
            "fab fa-rendact",
            "fab fa-renren",
            "fas fa-reply",
            "fas fa-reply-all",
            "fab fa-replyd",
            "fab fa-resolving",
            "fas fa-retweet",
            "fas fa-road",
            "fas fa-rocket",
            "fab fa-rocketchat",
            "fab fa-rockrms",
            "fas fa-rss",
            "fas fa-rss-square",
            "fas fa-ruble-sign",
            "fas fa-rupee-sign",
            "fab fa-safari",
            "fab fa-sass",
            "fas fa-save",
            "far fa-save",
            "fab fa-schlix",
            "fab fa-scribd",
            "fas fa-search",
            "fas fa-search-minus",
            "fas fa-search-plus",
            "fab fa-searchengin",
            "fab fa-sellcast",
            "fab fa-sellsy",
            "fas fa-server",
            "fab fa-servicestack",
            "fas fa-share",
            "fas fa-share-alt",
            "fas fa-share-alt-square",
            "fas fa-share-square",
            "far fa-share-square",
            "fas fa-shekel-sign",
            "fas fa-shield-alt",
            "fas fa-ship",
            "fab fa-shirtsinbulk",
            "fas fa-shopping-bag",
            "fas fa-shopping-basket",
            "fas fa-shopping-cart",
            "fas fa-shower",
            "fas fa-sign-in-alt",
            "fas fa-sign-language",
            "fas fa-sign-out-alt",
            "fas fa-signal",
            "fab fa-simplybuilt",
            "fab fa-sistrix",
            "fas fa-sitemap",
            "fab fa-skyatlas",
            "fab fa-skype",
            "fab fa-slack",
            "fab fa-slack-hash",
            "fas fa-sliders-h",
            "fab fa-slideshare",
            "fas fa-smile",
            "far fa-smile",
            "fab fa-snapchat",
            "fab fa-snapchat-ghost",
            "fab fa-snapchat-square",
            "fas fa-snowflake",
            "far fa-snowflake",
            "fas fa-sort",
            "fas fa-sort-alpha-down",
            "fas fa-sort-alpha-up",
            "fas fa-sort-amount-down",
            "fas fa-sort-amount-up",
            "fas fa-sort-down",
            "fas fa-sort-numeric-down",
            "fas fa-sort-numeric-up",
            "fas fa-sort-up",
            "fab fa-soundcloud",
            "fas fa-space-shuttle",
            "fab fa-speakap",
            "fas fa-spinner",
            "fab fa-spotify",
            "fas fa-square",
            "far fa-square",
            "fab fa-stack-exchange",
            "fab fa-stack-overflow",
            "fas fa-star",
            "far fa-star",
            "fas fa-star-half",
            "far fa-star-half",
            "fab fa-staylinked",
            "fab fa-steam",
            "fab fa-steam-square",
            "fab fa-steam-symbol",
            "fas fa-step-backward",
            "fas fa-step-forward",
            "fas fa-stethoscope",
            "fab fa-sticker-mule",
            "fas fa-sticky-note",
            "far fa-sticky-note",
            "fas fa-stop",
            "fas fa-stop-circle",
            "far fa-stop-circle",
            "fab fa-strava",
            "fas fa-street-view",
            "fas fa-strikethrough",
            "fab fa-stripe",
            "fab fa-stripe-s",
            "fab fa-studiovinari",
            "fab fa-stumbleupon",
            "fab fa-stumbleupon-circle",
            "fas fa-subscript",
            "fas fa-subway",
            "fas fa-suitcase",
            "fas fa-sun",
            "far fa-sun",
            "fab fa-superpowers",
            "fas fa-superscript",
            "fab fa-supple",
            "fas fa-sync",
            "fas fa-sync-alt",
            "fas fa-table",
            "fas fa-tablet",
            "fas fa-tablet-alt",
            "fas fa-tachometer-alt",
            "fas fa-tag",
            "fas fa-tags",
            "fas fa-tasks",
            "fas fa-taxi",
            "fab fa-telegram",
            "fab fa-telegram-plane",
            "fab fa-tencent-weibo",
            "fas fa-terminal",
            "fas fa-text-height",
            "fas fa-text-width",
            "fas fa-th",
            "fas fa-th-large",
            "fas fa-th-list",
            "fab fa-themeisle",
            "fas fa-thermometer-empty",
            "fas fa-thermometer-full",
            "fas fa-thermometer-half",
            "fas fa-thermometer-quarter",
            "fas fa-thermometer-three-quarters",
            "fas fa-thumbs-down",
            "far fa-thumbs-down",
            "fas fa-thumbs-up",
            "far fa-thumbs-up",
            "fas fa-thumbtack",
            "fas fa-ticket-alt",
            "fas fa-times",
            "fas fa-times-circle",
            "far fa-times-circle",
            "fas fa-tint",
            "fas fa-toggle-off",
            "fas fa-toggle-on",
            "fas fa-trademark",
            "fas fa-train",
            "fas fa-transgender",
            "fas fa-transgender-alt",
            "fas fa-trash",
            "fas fa-trash-alt",
            "far fa-trash-alt",
            "fas fa-tree",
            "fab fa-trello",
            "fab fa-tripadvisor",
            "fas fa-trophy",
            "fas fa-truck",
            "fas fa-tty",
            "fab fa-tumblr",
            "fab fa-tumblr-square",
            "fas fa-tv",
            "fab fa-twitch",
            "fab fa-twitter",
            "fab fa-twitter-square",
            "fab fa-typo3",
            "fab fa-uber",
            "fab fa-uikit",
            "fas fa-umbrella",
            "fas fa-underline",
            "fas fa-undo",
            "fas fa-undo-alt",
            "fab fa-uniregistry",
            "fas fa-universal-access",
            "fas fa-university",
            "fas fa-unlink",
            "fas fa-unlock",
            "fas fa-unlock-alt",
            "fab fa-untappd",
            "fas fa-upload",
            "fab fa-usb",
            "fas fa-user",
            "far fa-user",
            "fas fa-user-circle",
            "far fa-user-circle",
            "fas fa-user-md",
            "fas fa-user-plus",
            "fas fa-user-secret",
            "fas fa-user-times",
            "fas fa-users",
            "fab fa-ussunnah",
            "fas fa-utensil-spoon",
            "fas fa-utensils",
            "fab fa-vaadin",
            "fas fa-venus",
            "fas fa-venus-double",
            "fas fa-venus-mars",
            "fab fa-viacoin",
            "fab fa-viadeo",
            "fab fa-viadeo-square",
            "fab fa-viber",
            "fas fa-video",
            "fab fa-vimeo",
            "fab fa-vimeo-square",
            "fab fa-vimeo-v",
            "fab fa-vine",
            "fab fa-vk",
            "fab fa-vnv",
            "fas fa-volume-down",
            "fas fa-volume-off",
            "fas fa-volume-up",
            "fab fa-vuejs",
            "fab fa-weibo",
            "fab fa-weixin",
            "fab fa-whatsapp",
            "fab fa-whatsapp-square",
            "fas fa-wheelchair",
            "fab fa-whmcs",
            "fas fa-wifi",
            "fab fa-wikipedia-w",
            "fas fa-window-close",
            "far fa-window-close",
            "fas fa-window-maximize",
            "far fa-window-maximize",
            "fas fa-window-minimize",
            "fas fa-window-restore",
            "far fa-window-restore",
            "fab fa-windows",
            "fas fa-won-sign",
            "fab fa-wordpress",
            "fab fa-wordpress-simple",
            "fab fa-wpbeginner",
            "fab fa-wpexplorer",
            "fab fa-wpforms",
            "fas fa-wrench",
            "fab fa-xbox",
            "fab fa-xing",
            "fab fa-xing-square",
            "fab fa-y-combinator",
            "fab fa-yahoo",
            "fab fa-yandex",
            "fab fa-yandex-international",
            "fab fa-yelp",
            "fas fa-yen-sign",
            "fab fa-yoast",
            "fab fa-youtube"
        ];
        $str = '';
        foreach ($list as $class) {
            $name = explode("-", $class);
            $name1 = explode($name[0] . '-', $class);
            $str .= '<option value="' . $class . '"><i class="' . $class . '"></i> ' . $name1[1] . ' </option>';
        }

        return $str;
    }
}


if (!function_exists('socialIconList')) {
    function socialIconList()
    {
        $list = [
            'fa-facebook',
            'fa-twitter',
            'fa-linkedin',
            'fa-instagram',
            'fa-dribbble',
            'fa-google-plus',
            'fa-youtube',
            'fa-vimeo',
            'fa-reddit',

        ];
        $str = '';
        foreach ($list as $class) {
            $str .= '<option value="fab ' . $class . '"><i class="fa ' . $class . '"></i> ' . $class . ' </option>';
        }
        return $str;
    }
}


if (!function_exists('getProfileImage')) {
    function getProfileImage($path)
    {
        if (File::exists($path)) {
            return url($path);
        } else {
            return url('public/assets/profile/no_image_available.png');
        }
    }
}

if (!function_exists('getCourseImage')) {
    function getCourseImage($path)
    {
        if (File::exists($path)) {
            return url($path);
        } else {
            return url('public/assets/course/no_image.png');
        }
    }
}

if (!function_exists('getLogoImage')) {
    function getLogoImage($path)
    {
        if (File::exists($path)) {
            return url($path);
        } else {
            return url('public/uploads/settings/logo.png');
        }
    }
}


if (!function_exists('getTestimonialImage')) {
    function getTestimonialImage($path)
    {
        if (File::exists($path)) {
            return url($path);
        } else {
            return url('public/demo/testimonial/thumb/img.png');
        }
    }
}
if (!function_exists('getInstructorImage')) {
    function getInstructorImage($path)
    {
        if (File::exists($path)) {
            return url($path);
        } else {
            return url('public/demo/user/instructor.jpg');
        }
    }
}

if (!function_exists('getStudentImage')) {
    function getStudentImage($path)
    {
        if (File::exists($path)) {
            return url($path);
        } else {
            return url('public/demo/user/student.png');
        }
    }
}
if (!function_exists('getBlogImage')) {
    function getBlogImage($path)
    {
        if (File::exists($path)) {
            return url($path);
        } else {
            return url('public/demo/blog/demo.png');
        }
    }
}
if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }
}

if (!function_exists('isInstructor')) {
    function isInstructor()
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 2) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}


if (!function_exists('isStudent')) {
    function isStudent()
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 3) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('isFree')) {
    function isFree($course_id)
    {
        $course = \Modules\CourseSetting\Entities\Course::find($course_id);
        if ($course) {
            if ($course->price == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}


if (!function_exists('totalUnreadMessages')) {
    function totalUnreadMessages()
    {
        return \Modules\SystemSetting\Entities\Message::where('seen', '=', 0)->where('reciever_id', '=', Auth::id())->count();
    }
}


if (!function_exists('getLanguageList')) {
    function getLanguageList()
    {
        if (isModuleActive('LmsSaas')) {
            $domain = SaasDomain();
        } else {
            $domain = 'main';
        }
        return Cache::rememberForever('LanguageList_' . $domain, function () {
            return DB::table('languages')
                ->where('status', 1)
                ->select('code', 'name', 'native')
                ->where('lms_id', SaasInstitute()->id)
                ->get();
        });
    }
}


if (!function_exists('putEnvConfigration')) {
    function putEnvConfigration($envKey, $envValue)
    {
        $envValue = str_replace('\\', '\\' . '\\', $envValue);
        $value = '"' . $envValue . '"';
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n";
        $keyPosition = strpos($str, "{$envKey}=");


        if (is_bool($keyPosition)) {

            $str .= $envKey . '="' . $envValue . '"';

        } else {
            $endOfLinePosition = strpos($str, "\n", $keyPosition);
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            $str = str_replace($oldLine, "{$envKey}={$value}", $str);

            $str = substr($str, 0, -1);
        }

        if (!file_put_contents($envFile, $str)) {
            return false;
        } else {
            return true;
        }

    }
}


if (!function_exists('courseDetailsUrl')) {
    function courseDetailsUrl($id, $type, $slug)
    {
        if ($type == 1) {
            $details = 'courses-details';
        } elseif ($type == 2) {
            $details = 'quiz-details';
        } elseif ($type == 3) {
            $details = 'class-details';
        } else {
            $details = 'courses-details';
        }
        $url = url($details . '/' . $slug);
        return $url;
    }
}
if (!function_exists('UserEmailNotificationSetup')) {
    function UserEmailNotificationSetup($act, $user)
    {

        $role_email_template = RoleEmailTemplate::where('role_id', $user->role_id)->where('template_act', $act)->where('status', 1)->first();
        if ($role_email_template) {
            $user_notification_setup = UserNotificationSetup::where('user_id', $user->id)->first();
            if ($user_notification_setup) {
                $email_ids = explode(',', $user_notification_setup->email_ids);

                if (in_array($act, $email_ids)) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return true;
            }
        }
    }
}
if (!function_exists('UserBrowserNotificationSetup')) {
    function UserBrowserNotificationSetup($act, $user)
    {

        $role_email_template = RoleEmailTemplate::where('role_id', $user->role_id)->where('template_act', $act)->where('status', 1)->first();

        if ($role_email_template) {
            $user_notification_setup = UserNotificationSetup::where('user_id', $user->id)->first();

            if ($user_notification_setup) {
                $browser_ids = explode(',', $user_notification_setup->browser_ids);

                if (in_array($act, $browser_ids)) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return true;
            }
        }
    }
}
if (!function_exists('send_browser_notification')) {

    function send_browser_notification($user, $type, $shortcodes = [], $actionText, $actionURL)
    {
        $status = EmailTemplate::where('act', $type)->first()->status;
        if ($status == 1) {
            $email_template = EmailTemplate::where('act', $type)->where('status', 1)->first();

            if ($email_template->browser_message == null) {
                $message = $email_template->subj;
            } else {
                $message = $email_template->browser_message;
            }


            foreach ($shortcodes as $code => $value) {
                $message = shortcode_replacer('{{' . $code . '}}', $value, $message);
            }
            // $message = shortcode_replacer('{{footer}}', $general->email_template, $message);


            $details = [
                'title' => $email_template->subj,
                'body' => $message,
                'actionText' => $actionText,
                'actionURL' => $actionURL,
            ];
            Notification::send($user, new GeneralNotification($details));
        }

    }
}
if (!function_exists('htmlPart')) {
    function htmlPart($subject, $body)
    {
        $html = '
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
<style>

     .social_links {
        background: #F4F4F8;
        padding: 15px;
        margin: 30px 0 30px 0;
    }

    .social_links a {
        display: inline-block;
        font-size: 15px;
        color: #252B33;
        padding: 5px;
    }


</style>

<div class="">
<div style="color: rgb(255, 255, 255); font-family: &quot;Open Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; text-align: center; background-color: rgb(65, 80, 148); padding: 30px; border-top-left-radius: 3px; border-top-right-radius: 3px; margin: 0px;"><h1 style="margin: 20px 0px 10px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-weight: 500; line-height: 1.1; color: inherit; font-size: 36px;">
' . $subject . '

</h1></div><div style="color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; padding: 20px;">
<p style="color: rgb(85, 85, 85);"><br></p>
<p style="color: rgb(85, 85, 85);">' . $body . '</p></div>
</div>

<div class="email_invite_wrapper" style="text-align: center">


    <div class="social_links">
        <a href="https://twitter.com/codetheme"> <i class="fab fa-facebook-f"></i> </a>
        <a href="https://codecanyon.net/user/codethemes/portfolio"><i class="fas fa-code"></i> </a>
        <a href="https://twitter.com/codetheme" target="_blank"> <i class="fab fa-twitter"></i> </a>
        <a href="https://dribbble.com/codethemes"> <i class="fab fa-dribbble"></i></a>
    </div>
</div>

';
        return $html;
    }
}

if (!function_exists('getPriceFormat')) {
    function getPriceFormat($price)
    {
        $symbol = Settings('currency_symbol');
        $type = Settings('currency_show');
        if (!empty($price) || $price != 0) {
            $price = number_format((float)str_replace(',', '', $price), 2);

            if ($type == 1) {
                $result = $symbol . $price;

            } elseif ($type == 2) {
                $result = $symbol . ' ' . $price;

            } elseif ($type == 3) {
                $result = $price . $symbol;

            } elseif ($type == 4) {
                $result = $price . ' ' . $symbol;

            } else {
                $result = $price;
            }
        } else {
            $result = trans('common.Free');
        }

        return $result;
    }
}


if (!function_exists('totalQuizQus')) {
    function totalQuizQus($quiz_id)
    {
        $total = \Modules\Quiz\Entities\OnlineExamQuestionAssign::where('online_exam_id', $quiz_id)->count();
        return $total;
    }
}

if (!function_exists('totalQuizMarks')) {
    function totalQuizMarks($quiz_id)
    {
        $totalMark = 0;
        $total = \Modules\Quiz\Entities\OnlineExamQuestionAssign::where('online_exam_id', $quiz_id)->with('questionBank')->get();

        foreach ($total as $question) {
            $totalMark = $totalMark + $question->questionBank->marks;
        }
        return $totalMark;
    }
}

if (!function_exists('theme')) {
    function theme($fileName)
    {
        if (!empty(Settings('frontend_active_theme'))) {
            $theme = Settings('frontend_active_theme');
        } else {
            $theme = 'infixlmstheme';
        }
        return 'frontend.' . $theme . '.' . $fileName;

    }
}

//Start Compact Helper

if (!function_exists('topbarSetting')) {
    function topbarSetting()
    {
        return app()->topbarSetting;
    }
}
if (!function_exists('courseSetting')) {
    function courseSetting()
    {
        return CourseSetting::getData();
    }
}
if (!function_exists('itemsGridSize')) {
    function itemsGridSize()
    {
        if (courseSetting()->size_of_grid == 3) {
            $view_grid = 4;
        } else {
            $view_grid = 3;
        }

        return $view_grid * 3;
    }
}
//End Compact Helper

if (!function_exists('Settings')) {
    function Settings($value = null)
    {
        try {
            if (isModuleActive('LmsSaas')) {
                $domain = SaasDomain();
            } else {
                $domain = 'main';
            }
            if ($value == "frontend_active_theme") {
                return Cache::rememberForever('frontend_active_theme_' . $domain, function () {
                    $setting = GeneralSetting::where('key', 'frontend_active_theme')->first();
                    return $setting->value;
                });
            } elseif ($value == "active_time_zone") {
                if (!isValidTimeZone(app('getSetting')[$value])) {
                    return 'Asia/Dhaka';
                }
            }
            return app('getSetting')[$value];
        } catch (Exception $exception) {
            return false;
        }
    }
}
if (!function_exists('isValidTimeZone')) {
    function isValidTimeZone($timezone = null)
    {
        try {
            Carbon::now($timezone);
        } catch (Exception $exception) {
            return false;
        }
        return true;
    }
}

if (!function_exists('isModuleActive')) {
    function isModuleActive($module)
    {

        try {
            $haveModule = app('ModuleList')->where('name', $module)->first();

            if (empty($haveModule)) {
                return false;
            }
            $modulestatus = $haveModule->status;


            $is_module_available = 'Modules/' . $module . '/Providers/' . $module . 'ServiceProvider.php';

            if (file_exists($is_module_available)) {


                $moduleCheck = \Nwidart\Modules\Facades\Module::find($module)->isEnabled();

                if (!$moduleCheck) {
                    return false;
                }

                if ($modulestatus == 1) {
                    $is_verify = app('ModuleManagerList')->where('name', $module)->first();

                    if (!empty($is_verify->purchase_code)) {
                        return true;
                    }
                }
            }

//            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }

    }
}


if (!function_exists('getPercentageRating')) {
    function getPercentageRating($review_data, $value)
    {
        if ($review_data['total'] > 0) {
            $data['total'] = $review_data['total'] ?? 0;
            switch ($value) {
                case 1 :
                    $per = $review_data['1'];
                    break;
                case 2 :
                    $per = $review_data['2'];
                    break;
                case 3 :
                    $per = $review_data['3'];
                    break;
                case 4 :
                    $per = $review_data['4'];
                    break;
                case 5 :
                    $per = $review_data['5'];
                    break;
                default:
                    $per = 0;
                    break;
            }

            if ($per > 0) {
                $data['per'] = ($per / $data['total']) * 100;
            } else {
                $data['per'] = 0;
            }
        } else {
            $data['per'] = 0;
        }
        $data['per'] = number_format($data['per'], 2);
        return $data['per'] ?? 0;
    }
}

if (!function_exists('userRating')) {
    function userRating($user_id)
    {
        $totalRatings['rating'] = 0;
        $ReviewList = DB::table('courses')
            ->join('course_reveiws', 'course_reveiws.course_id', 'courses.id')
            ->select('courses.id', 'course_reveiws.id as review_id', 'course_reveiws.star as review_star')
            ->where('courses.user_id', $user_id)
            ->get();
        $totalRatings['total'] = count($ReviewList);

        foreach ($ReviewList as $Review) {
            $totalRatings['rating'] += $Review->review_star;
        }

        if ($totalRatings['total'] != 0) {
            $avg = ($totalRatings['rating'] / $totalRatings['total']);
        } else {
            $avg = 0;
        }

        if ($avg != 0) {
            if ($avg - floor($avg) > 0) {
                $rate = number_format($avg, 1);
            } else {
                $rate = number_format($avg, 0);
            }
            $totalRatings['rating'] = $rate;
        }
        return $totalRatings;
    }
}


if (!function_exists('getPriceWithConversion')) {
    function getPriceWithConversion($price)
    {
        $price = str_replace(',', '', $price);
        $price = $price * 1;
        return $price;
    }
}


function convertCurrency($from_currency, $to_currency, $amount)
{
    $from = urlencode($from_currency);
    $to = urlencode($to_currency);
    $apikey = Settings('fixer_key') ?? '0bd244e811264242d56e1759c93a3f1a';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://data.fixer.io/api/latest?access_key=" . $apikey,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/json"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {

        return number_format($amount, 2, '.', '');
    } else {

        $info = json_decode($response);
        $cur = (array)@$info->rates;
        $from_ = null;
        $to_ = null;
        foreach ($cur as $key => $value) {
            if ($key == $from) {
                $from_ = $value;
            }
            if ($key == $to) {
                $to_ = $value;
            }

        }
        if ($to_ > 0) {
            $total = ($to_ / $from_) * $amount;
        } else {
            $total = $amount;
        }

        return number_format($total, 2, '.', '');
    }
}

if (!function_exists('isRtl')) {
    function isRtl()
    {
        if (Auth::check()) {
            $rtl = Auth::user()->language_rtl;
        } elseif (\Illuminate\Support\Facades\Session::get('locale')) {
            $rtl = \Illuminate\Support\Facades\Session::get('language_rtl');
        } else {
            $rtl = Settings('language_rtl');
        }

        if ($rtl == 1) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('getDomainName')) {
    function getDomainName($url)
    {
        $url_domain = preg_replace("(^https?://)", "", $url);
        $url_domain = preg_replace("(^http?://)", "", $url_domain);
        $url_domain = str_replace("/", "", $url_domain);
        return $url_domain;

    }
}

if (!function_exists('getMenuLink')) {
    function getMenuLink($menu)
    {
        $url = asset('/');
        if ($menu) {
            if (!empty($menu->link)) {
                if (substr($menu->link, 0, 1) == '/') {
                    if ($menu->link == "/") {
                        return url($menu->link) . '/';

                    }
                    return url($menu->link);
                }
                return $menu->link;
            }
            $type = $menu->type;
            $element_id = $menu->element_id;
            if ($type == "Dynamic Page") {

                $page = \Modules\FrontendManage\Entities\FrontPage::find($element_id);
                if ($page) {
                    $url = \route('frontPage', [$page->slug]);
                }
            } elseif ($type == "Static Page") {
                $page = \Modules\FrontendManage\Entities\FrontPage::find($element_id);
                if ($page) {
                    $url = url($page->slug);

                }
            } elseif ($type == "Category") {
                $url = route('courses') . "?category=" . $element_id;

            } elseif ($type == "Sub Category") {
                $url = route('courses') . "?category=" . $element_id;

            } elseif ($type == "Course") {
                $course = \Modules\CourseSetting\Entities\Course::find($element_id);
                if ($course) {
                    $url = route('courseDetailsView', [$course->id, $course->slug]);

                }
            } elseif ($type == "Quiz") {
                $course = \Modules\CourseSetting\Entities\Course::find($element_id);
                if ($course) {
                    $url = route('classDetails', [$course->id, $course->slug]);

                }
            } elseif ($type == "Class") {
                $course = \Modules\CourseSetting\Entities\Course::find($element_id);
                if ($course) {
                    $url = route('courseDetailsView', [$course->id, $course->slug]);

                }
            } elseif ($type == "Custom Link") {
                $url = '';
            }
        }


        return $url;

    }
}

if (!function_exists('isSubscribe')) {
    function isSubscribe()
    {
        if (isModuleActive('Subscription') && Auth::check()) {
            $user = Auth::user();
            $date_of_subscription = $user->subscription_validity_date;
            if (empty($date_of_subscription)) {
                return false;
            }

            $expires_at = new DateTime($date_of_subscription);
            $today = new DateTime('now');


            if ($expires_at < $today)
                return false;

            else {
                return true;
            }
        } else {
            return false;
        }

        return false;

    }
}


if (!function_exists('userCurrentPlan')) {
    function userCurrentPlan()
    {
        if (isModuleActive('Subscription')) {
            if (Auth::check()) {
                $user = Auth::user();
                $date_of_subscription = $user->subscription_validity_date;
                if (empty($date_of_subscription)) {
                    return null;
                }

                $check = SubscriptionCheckout::select('plan_id')->where('end_date', '>=', date('Y-m-d'))->get();
                if (count($check) != 0) {
                    $plan = [];
                    foreach ($check as $p) {
                        $plan[] = $p->plan_id;
                    }
                    return $plan;
                }


            }
        } else {
            return null;
        }

        return null;

    }
}
if (!function_exists('hasTable')) {
    function hasTable($table)
    {
        if (Schema::hasTable($table)) {
            return true;
        } else {
            return false;
        }

    }
}


if (!function_exists('reviewCanDelete')) {
    function reviewCanDelete($review_user_id, $instructor_id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($review_user_id == $user->id || $user->role_id == 1 || $instructor_id == $user->id) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }
}
if (!function_exists('commentCanDelete')) {
    function commentCanDelete($comment_user_id, $instructor_id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($comment_user_id == $user->id || $user->role_id == 1 || $instructor_id == $user->id) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }
}
if (!function_exists('blogCommentCanDelete')) {
    function blogCommentCanDelete()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role_id == 1) {
                return true;
            }
        }
        return false;
    }
}
if (!function_exists('ReplyCanDelete')) {
    function ReplyCanDelete($reply_user_id, $instructor_id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($reply_user_id == $user->id || $user->role_id == 1 || $instructor_id == $user->id) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}


if (!function_exists('hasTax')) {
    function hasTax()
    {
        if (isModuleActive('Tax')) {
            if (Settings('tax_status') == 1) {
                return true;
            } else {
                return false;
            }

        }
        return false;
    }
}

if (!function_exists('countryWishTaxRate')) {
    function countryWishTaxRate()
    {
        $vat = 0;
        if (Auth::check()) {
            $country_id = Auth::user()->country;

            $countryWishTaxList = Cache::rememberForever('countryWishTaxList_' . SaasDomain(), function () {
                return DB::table('country_wish_taxes')
                    ->select('country_id', 'tax')
                    ->where('status', 1)
                    ->get();
            });

            $setting = $countryWishTaxList->where('country_id', $country_id)->first();
            if ($setting) {
                $vat = $setting->tax;
            }
        }
        return $vat;
    }
}
if (!function_exists('applyTax')) {
    function applyTax($price)
    {
        $type = Settings('tax_type');
        if ($type == 1) {
            $vat = Settings('tax_percentage');
        } else {
            $vat = countryWishTaxRate();
        }
        $vatToPay = ($price / 100) * $vat;
        $totalPrice = $price + $vatToPay;

        return $totalPrice;

    }
}
if (!function_exists('taxAmount')) {
    function taxAmount($price)
    {
        $type = Settings('tax_type');
        if ($type == 1) {
            $vat = Settings('tax_percentage');
        } else {
            $vat = countryWishTaxRate();
        }
        $vatToPay = ($price / 100) * $vat;
        return $vatToPay;

    }
}

if (!function_exists('getPriceAsNumber')) {
    function getPriceAsNumber($price)
    {
        return str_replace(',', '', $price);

    }
}


if (!function_exists('currentTheme')) {
    function currentTheme()
    {
        if (app()->bound('getSetting')) {
            return Theme::where('is_active', 1)->first()->name;
        } else {
            return 'infixlmstheme';
        }


    }
}

if (!function_exists('shortDetails')) {
    function shortDetails($string, $length)
    {
        $string = strip_tags($string);
        if (strlen($string) > $length) {

            // truncate string
            $stringCut = substr($string, 0, $length);
            $endPoint = strrpos($stringCut, ' ');

            //if the string doesn't contain any space then it will cut without word basis.
            $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
            $string .= '...';
        }
        return $string;
    }
}

if (!function_exists('totalReviews')) {
    function totalReviews($course_id)
    {
        return \Modules\CourseSetting\Entities\CourseReveiw::where('course_id', $course_id)->count();
    }
}

if (!function_exists('validationMessage')) {
    function validationMessage($validation_rules)
    {
        $message = [];
        foreach ($validation_rules as $attribute => $rules) {

            if (is_array($rules)) {
                $single_rule = $rules;
            } else {
                $single_rule = explode('|', $rules);
            }

            foreach ($single_rule as $rule) {
                $string = explode(':', $rule);
                $message [$attribute . '.' . $string[0]] = __('validation.' . $attribute . '.' . $string[0]);
            }
        }

        return $message;
    }
}

if (!function_exists('escapHtmlChar')) {
    function escapHtmlChar($str)
    {
        $find = ['"', "'"];
        $replace = ['&quot;', '&apos;'];
        return str_replace($find, $replace, htmlspecialchars($str));


    }
}
if (!function_exists('doubleQuotes2singleQuotes')) {
    function doubleQuotes2singleQuotes($str)
    {
        $find = ['"'];
        $replace = ["'"];
        return str_replace($find, $replace, htmlspecialchars($str));


    }
}


if (!function_exists('showDate')) {
    function showDate($date)
    {
        if (!$date) {
            return;
        }
        try {
            return Carbon::parse($date)->locale(app()->getLocale())->translatedFormat(Settings('active_date_format'));
        } catch (\Exception $e) {
            return;
        }
    }
}

if (!function_exists('checkParent')) {
    function checkParent($category, $string = null)
    {
        if (!empty($category->parent->id)) {
            return checkParent($category->parent, $string . '-');
        }
        if ($string) {
            $string = $string . '>';
        }
        return $string;

    }
}


if (!function_exists('GettingError')) {
    function GettingError($message, $url = '', $ip = '', $agent = '', $return = false)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        } else {
            $user_id = 0;
        }
        \Modules\Setting\Entities\ErrorLog::create([
            'subject' => $message,
            'type' => '1',
            'url' => $url,
            'ip' => $ip,
            'agent' => $agent,
            'user_id' => $user_id,
        ]);
        if ($return) {
            return false;
        } else {
            abort('500', trans('frontend.Something went wrong, Please check error log'));
        }

    }
}

if (!function_exists('isViewable')) {
    function isViewable($course)
    {
        $isViewable = true;
        if ($course->status == 0) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role_id != 1 && $course->user_id != $user->id) {
                    $isViewable = false;
                }
            } else {
                $isViewable = false;
            }
        }
        return $isViewable;


    }
}

if (!function_exists('MinuteFormat')) {
    function MinuteFormat($minutes)
    {
        $minutes = intval($minutes);
        $hours = floor($minutes / 60);
        $min = $minutes - ($hours * 60);
        $result = '';
        if ($hours == 1) {
            $result .= $hours . ' Hour ';
        } elseif ($hours > 1) {
            $result .= $hours . ' Hours ';
        }

        if ($min == 1) {
            $result .= $min . ' Min';
        } elseif ($min > 1) {
            $result .= $min . ' Min';
        }

        return $result;
    }
}

if (!function_exists('UpdateGeneralSetting')) {
    function UpdateGeneralSetting($key, $value)
    {
        $setting = GeneralSetting::where('key', $key)->first();
        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new GeneralSetting();
            $setting->key = $key;
            $setting->value = $value;
            $setting->updated_at = now();
            $setting->created_at = now();
            $setting->save();
        }
    }
}

if (!function_exists('GenerateGeneralSetting')) {
    function GenerateGeneralSetting($domain)
    {
        if (Schema::hasColumn('general_settings', 'key')) {
            $path = Storage::path('settings.json');
            $settings = GeneralSetting::get(['key', 'value'])->pluck('value', 'key')->toArray();
            if (!Storage::has('settings.json')) {
                $strJsonFileContents = null;
            } else {
                $strJsonFileContents = file_get_contents($path);

            }
            $file_data = json_decode($strJsonFileContents, true);
            $setting_array[$domain] = $settings;
            if (!empty($file_data)) {
                $merged_array = array_merge($file_data, $setting_array);
            } else {
                $merged_array = $setting_array;
            }
            $merged_array = json_encode($merged_array, JSON_PRETTY_PRINT);
            file_put_contents($path, $merged_array);

        }
    }
}

if (!function_exists('GenerateHomeContent')) {
    function GenerateHomeContent($domain)
    {

        if (Schema::hasColumn('home_contents', 'key')) {
            $path = Storage::path('homeContent.json');
            $settings = HomeContent::get(['key', 'value'])->pluck('value', 'key')->toArray();
            if (!Storage::has('homeContent.json')) {
                $strJsonFileContents = null;
            } else {
                $strJsonFileContents = file_get_contents($path);
            }
            $file_data = json_decode($strJsonFileContents, true);
            $setting_array[$domain] = $settings;
            if (!empty($file_data)) {
                $merged_array = array_merge($file_data, $setting_array);
            } else {
                $merged_array = $setting_array;
            }
            $merged_array = json_encode($merged_array, JSON_PRETTY_PRINT);
            file_put_contents($path, $merged_array);
        }
    }
}


if (!function_exists('UpdateHomeContent')) {
    function UpdateHomeContent($key, $value)
    {
        $setting = HomeContent::where('key', $key)->first();
        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new HomeContent();
            $setting->key = $key;
            $setting->value = $value;
            $setting->updated_at = now();
            $setting->created_at = now();
            $setting->save();
        }
    }
}

if (!function_exists('getIpBlockList')) {
    function getIpBlockList()
    {
        if (isModuleActive('LmsSaas')) {
            $domain = SaasDomain();
        } else {
            $domain = 'main';
        }

        Cache::rememberForever('ipBlockList_' . $domain, function () {
            $data = [];
            $rowData = IpBlock::select('ip_address')->get();
            foreach ($rowData as $single) {
                $data[] = $single['ip_address'];
            }
            return $data;
        });
    }
}


if (!function_exists('HomeContents')) {
    function HomeContents($value = null)
    {
        return app('getHomeContent')->{$value};
    }
}

if (!function_exists('generateBlockPosition')) {
    function generateBlockPosition()
    {
        $homepage_block_positions = DB::table('homepage_block_positions')->orderBy('order', 'asc')->get();
        UpdateHomeContent('homepage_block_positions', json_encode($homepage_block_positions));
    }
}
if (!function_exists('isBundleValid')) {
    function isBundleExpire($course_id)
    {
        $enroll = \Modules\CourseSetting\Entities\CourseEnrolled::where('user_id', Auth::user()->id)->where('course_id', $course_id)->first();
        if ($enroll) {
            $validity = $enroll->bundle_course_validity;
            if (!empty($validity)) {
                if (!Carbon::parse($validity)->isFuture()) {
                    return true;
                }
            }

        }

        return false;
    }
}


if (!function_exists('orgSubscriptionCourseValidity')) {
    function orgSubscriptionCourseValidity($courseId)
    {
        if (Auth::user()->role_id == 3) {
            if (isModuleActive('OrgSubscription') && Auth::check()) {
                $enroll = \Modules\CourseSetting\Entities\CourseEnrolled::where('course_id', $courseId)->where('user_id', Auth::id())->first();

                if ($enroll && $enroll->subscription == 1) {
                    $time = $enroll->subscription_validity_time;
                    if (!empty($time)) {
                        $validity = $enroll->subscription_validity_date;
                    } else {
                        $validity = $enroll->subscription_validity_date . ' ' . $time;
                    }


                    if (!empty($validity)) {
                        if (!Carbon::parse($validity)->isFuture()) {
                            return false;
                        }
                    }

                }


            }
        }

        return true;
    }
}


if (!function_exists('orgSubscriptionCourseSequence')) {
    function orgSubscriptionCourseSequence($courseId)
    {
        if (isModuleActive('OrgSubscription') && Auth::check()) {

            $org_subscription_checkouts = OrgSubscriptionCheckout::where('user_id', Auth::id())->get();
            $access_courses = [];
            $plan_id = '';
            foreach ($org_subscription_checkouts as $cko) {
                if ($cko->plan->type == 1) {
                    if ($cko->plan->sequence == 1 && date('Y-m-d', strtotime($cko->plan->end_date)) > date('Y-m-d')) {
                        foreach ($cko->plan->assign as $course) {
                            if ($course->course_id == $courseId) {
                                $plan_id = $course->plan_id;
                            }
                        }
                    }

                } else {
                    $end_date = Carbon::parse($cko->start_date)->addDays($cko->days);
                    if ($cko->plan->sequence == 1 && $end_date->format('Y-m-d') > date('Y-m-d')) {
                        foreach ($cko->plan->assign as $course) {
//                            $access_courses[] = $course->course_id;
                            if ($course->course_id == $courseId) {
                                $plan_id = $course->plan_id;
                            }

                        }
                    }

                }
            }
            if ($plan_id) {
                $plan = OrgCourseSubscription::find($plan_id);
                if ($plan) {
                    foreach ($plan->assign as $course) {
                        $access_courses[] = $course->course_id;
                        if ($course->loginUserTotalPercentage != 100) {
                            break;
                        }
                    }
                }

            } else {
                return true;
            }

            if (in_array($courseId, $access_courses)) {
                return true;
            }
        }
        return false;
    }
}


if (!function_exists('updateEnrolledCourseLastView')) {
    function updateEnrolledCourseLastView($course_id)
    {
        if (Auth::check()) {
            $enroll = \Modules\CourseSetting\Entities\CourseEnrolled::where('course_id', $course_id)->where('course_id', $course_id)->first();
            if ($enroll) {
                $enroll->last_view_at = now();
                $enroll->save();
            }
        }
    }
}

if (!function_exists('dateConvert')) {

    function dateConvert($input_date)
    {
        try {
            $system_date_format = session()->get('system_date_format');
            if (empty($system_date_format)) {
                $date_format_id = SmGeneralSettings::where('id', 1)->first(['date_format_id'])->date_format_id;
                $system_date_format = SmDateFormat::where('id', $date_format_id)->first(['format'])->format;
                session()->put('system_date_format', $system_date_format);
                return date_format(date_create($input_date), $system_date_format);
            } else {
                return date_format(date_create($input_date), $system_date_format);
            }
        } catch (\Throwable $th) {
            return $input_date;
        }
    }
}

if (!function_exists('canApprove')) {
    function canApprove($staff_id = null)
    {
        if (Auth::user()->role_id == 1)
            return true;
        else {
            if ($staff_id) {
                $staff = Staff::find($staff_id);
                if ($staff) {
                    $department = Department::find($staff->department_id);
                    if ($department && $department->user_id && in_array(auth()->id(), $department->user_id))
                        return true;
                }
            }
            return false;
        }
    }
}


if (!function_exists('attendanceCheck')) {
    function attendanceCheck($user_id, $type, $date)
    {
        $attendance = Attendance::where('user_id', $user_id)->whereDate('date', \Carbon\Carbon::parse($date)->format('Y-m-d'))->first();
        if ($attendance != null) {
            if ($attendance->attendance == $type) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
if (!function_exists('attendanceInfo')) {
    function attendanceInfo($user_id, $date)
    {
        $attendance = Attendance::where('user_id', $user_id)->whereDate('date', \Carbon\Carbon::parse($date)->format('Y-m-d'))->first();

        return $attendance;
    }
}


if (!function_exists('attendanceNote')) {
    function attendanceNote($user_id)
    {
        $todayAttendance = Attendance::where('user_id', $user_id)->where('date', \Carbon\Carbon::today()->toDateString())->first();
        if ($todayAttendance != null) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('attendanceNoteDateWise')) {
    function attendanceNoteDateWise($user_id, $date)
    {
        $todayAttendance = Attendance::where('user_id', $user_id)->where('date', date('Y-m-d', strtotime($date)))->first();
        if ($todayAttendance != null) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('LateNote')) {
    function LateNote($user_id, $date)
    {
        $todayAttendance = Attendance::where('user_id', $user_id)->where('date', date('Y-m-d', strtotime($date)))->first();
        if ($todayAttendance) {
            return $todayAttendance->late_note;
        } else {
            return '';
        }
    }
}


if (!function_exists('Note')) {
    function Note($user_id)
    {
        $todayAttendance = Attendance::where('user_id', $user_id)->where('date', \Carbon\Carbon::today()->toDateString())->first();
        if ($todayAttendance != null && $todayAttendance->note != null) {
            return $todayAttendance->note;
        } else {
            return false;
        }
    }
}
if (!function_exists('NoteDateWise')) {
    function NoteDateWise($user_id, $date)
    {
        $todayAttendance = Attendance::where('user_id', $user_id)->where('date', date('Y-m-d', strtotime($date)))->first();
        if ($todayAttendance != null && $todayAttendance->note != null) {
            return $todayAttendance->note;
        } else {
            return false;
        }
    }
}

if (!function_exists('transformExcelDate')) {
    function transformExcelDate($value, $format = 'd/m/Y')
    {
        try {
            $date = \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            return $date->format($format);
        } catch (\ErrorException $e) {
            $date = \Carbon\Carbon::createFromFormat($format, $value);
            return $date->format($format);
        }
    }
}


if (!function_exists('assignStaffToUser')) {
    function assignStaffToUser($user)
    {
        $check = DB::table('staffs')->where('user_id', $user->id)->first();
        if ($check) {
            DB::table('staffs')->insert([
                'user_id' => $user->id
            ]);
        }
    }
}


if (!function_exists('generateUniqueId')) {
    function generateUniqueId($random_id_length = 10)
    {
        $rnd_id = Hash::make((uniqid(rand(), 1)));
        $rnd_id = strip_tags(stripslashes($rnd_id));
        $rnd_id = str_replace(".", "", $rnd_id);
        $rnd_id = strrev(str_replace("/", "", $rnd_id));
        return substr($rnd_id, 0, $random_id_length);
    }
}

if (!function_exists('updateModuleParentRoute')) {
    function updateModuleParentRoute()
    {
        if (Schema::hasColumn('permissions', 'parent_route')) {
            $permissions = DB::table('permissions')->whereNotNull('parent_id')->get(['parent_id', 'route']);
            foreach ($permissions as $permission) {
                $parent_route = null;
                if (!empty($permission->parent_id)) {
                    $parent = DB::table('permissions')->where('id', $permission->parent_id)->first();
                    if ($parent) {
                        $parent_route = $parent->route;
                    }
                }
                DB::table('permissions')
                    ->where('route', $permission->route)->update([
                        'parent_route' => $parent_route,
                    ]);
            }
            Cache::forget('PermissionList');
            Cache::forget('RoleList');
            Cache::forget('PolicyPermissionList');
            Cache::forget('PolicyRoleList');
        }

    }
}


if (!function_exists('paymentGateWayCredentialsEmptyCheck')) {
    function paymentGateWayCredentialsEmptyCheck($method)
    {
        if ($method == 'PayPal') {
            if (!empty(getPaymentEnv('PAYPAL_CLIENT_ID')) && !empty(getPaymentEnv('PAYPAL_CLIENT_SECRET')) && !empty(getPaymentEnv('IS_PAYPAL_LOCALHOST'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'Instamojo') {
            if (!empty(getPaymentEnv('Instamojo_API_AUTH')) && !empty(getPaymentEnv('Instamojo_API_AUTH_TOKEN')) && !empty(getPaymentEnv('Instamojo_URL'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'Midtrans') {
            if (!empty(getPaymentEnv('MIDTRANS_SERVER_KEY'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'Payeer') {
            if (!empty(getPaymentEnv('PAYEER_MERCHANT')) && !empty(getPaymentEnv('PAYEER_KEY'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'Pesapal') {
            if (!empty(getPaymentEnv('PESAPAL_KEY')) && !empty(getPaymentEnv('PESAPAL_SECRET'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'Mobilpay') {
            if (!empty(getPaymentEnv('MOBILPAY_MERCHANT_ID')) && !empty(getPaymentEnv('MOBILPAY_TEST_MODE')) && !empty(getPaymentEnv('MOBILPAY_PUBLIC_KEY_PATH')) && !empty(getPaymentEnv('MOBILPAY_PRIVATE_KEY_PATH'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'PayPal') {
            if (!empty(getPaymentEnv('PAYPAL_CLIENT_ID')) && !empty(getPaymentEnv('PAYPAL_CLIENT_SECRET'))) {
                $result = true;
            } else {
                $result = false;
            }
        }elseif ($method == 'Stripe') {
            if (!empty(getPaymentEnv('STRIPE_SECRET')) && !empty(getPaymentEnv('STRIPE_KEY'))) {
                $result = true;
            } else {
                $result = false;
            }
        }elseif ($method == 'PayStack') {
            if (!empty(getPaymentEnv('PAYSTACK_PUBLIC_KEY')) && !empty(getPaymentEnv('PAYSTACK_SECRET_KEY')) && !empty(getPaymentEnv('MERCHANT_EMAIL'))&& !empty(getPaymentEnv('PAYSTACK_PAYMENT_URL'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'RazorPay') {
            if (!empty(getPaymentEnv('RAZOR_KEY')) && !empty(getPaymentEnv('RAZOR_SECRET'))) {
                $result = true;
            } else {
                $result = false;
            }
        }  elseif ($method == 'MercadoPago') {
            if (!empty(getPaymentEnv('MERCADO_PUBLIC_KEY')) && !empty(getPaymentEnv('MERCADO_ACCESS_TOKEN'))) {
                $result = true;
            } else {
                $result = false;
            }
        }  elseif ($method == 'PayTM') {
            if (!empty(getPaymentEnv('PAYTM_MERCHANT_ID')) && !empty(getPaymentEnv('PAYTM_MERCHANT_KEY')) && !empty(getPaymentEnv('PAYTM_MERCHANT_WEBSITE')) && !empty(getPaymentEnv('PAYTM_CHANNEL')) && !empty(getPaymentEnv('PAYTM_INDUSTRY_TYPE'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($method == 'Bkash') {
            if (!empty(getPaymentEnv('BKASH_APP_KEY')) && !empty(getPaymentEnv('BKASH_APP_SECRET')) && !empty(getPaymentEnv('BKASH_USERNAME')) && !empty(getPaymentEnv('BKASH_PASSWORD')) && !empty(getPaymentEnv('IS_BKASH_LOCALHOST'))) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = true;
        }
        return $result;
    }
}



if (!function_exists('affiliateConfig')) {
    function affiliateConfig($key)
    {
        try {
            if($key){
                if (Cache::has('affiliate_config_'.SaasDomain())) {
                    $affiliate_configs =  Cache::get('affiliate_config_'.SaasDomain());
                    return $affiliate_configs[$key];

                } else {
                    Cache::forget('affiliate_config_'.SaasDomain());
                    $datas = [];
                    foreach (\Modules\Affiliate\Entities\AffiliateConfiguration::get() as  $setting) {
                        $datas[$setting->key] = $setting->value;
                    }
                    Cache::rememberForever('affiliate_config_'.SaasDomain(), function () use($datas) {
                        return $datas;
                    });
                    $affiliate_configs =  Cache::get('affiliate_config_'.SaasDomain());
                    return $affiliate_configs[$key];
                }
            }else{
                return false;
            }

        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('showPrice')) {
    function showPrice($price)
    {
        $symbol = Settings('currency_symbol');
        $type = Settings('currency_show');
        if (!empty($price) || $price != 0) {

            $price = number_format(str_replace(',', '', $price), 2);

            if ($type == 1) {
                $result = $symbol . $price;

            } elseif ($type == 2) {
                $result = $symbol . ' ' . $price;

            } elseif ($type == 3) {
                $result = $price . $symbol;

            } elseif ($type == 4) {
                $result = $price . ' ' . $symbol;

            } else {
                $result = $price;
            }
        } else {
            $result = 0;
        }
        return $result;
    }
}

?>
