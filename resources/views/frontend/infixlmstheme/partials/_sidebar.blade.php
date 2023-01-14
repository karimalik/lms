<!-- sidebar part here -->
<nav class="sidebar">
    <div class="logo d-flex justify-content-between">
        <a href="{{url('/')}}"><img style="width: 200px" src="{{getCourseImage(Settings('logo') )}}"
                                    alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <div class="sidebar_iner">
        <ul class="list-unstyled">
            @if (permissionCheck('studentDashboard'))
                <li>
                    <a href="{{route('studentDashboard')}}"
                       class="  d-flex align-items-center {{ routeIs('studentDashboard')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/dashboard.svg" alt="">
                        </div>
                        <span>{{__('common.Dashboard')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('myCourses'))

                <li>
                    <a href="{{route('myCourses')}}"
                       class=" d-flex align-items-center {{ routeIs('myCourses')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/my_course.svg" alt="">
                        </div>
                        <span>{{__('common.My Courses')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('myQuizzes'))
                <li>
                    <a href="{{route('myQuizzes')}}"
                       class=" d-flex align-items-center {{ routeIs('myQuizzes')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/my_course.svg" alt="">
                        </div>
                        <span>{{__('common.My Quizzes')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('myClasses'))

                <li>
                    <a href="{{route('myClasses')}}"
                       class=" d-flex align-items-center {{ routeIs('myClasses')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/my_quiz.svg" alt="">
                        </div>
                        <span>{{__('common.Live Classes')}}</span>
                    </a>
                </li>
            @endif

            @if(isModuleActive('OrgSubscription'))
                <li>
                    <a href="{{route('orgSubscriptionCourses')}}"
                       class=" d-flex align-items-center {{ routeIs('orgSubscriptionCourses')  ? 'active' : '' }}">
                        <span>{{__('org-subscription.My Plan')}}</span>
                    </a>
                </li>
            @endif

            @if(isModuleActive('Homework'))
                <li>
                    <a href="{{route('myHomework')}}"
                       class=" d-flex align-items-center {{ routeIs('myHomework')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/my_quiz.svg" alt="">
                        </div>
                        <span>{{__('homework.Study Material')}}</span>
                    </a>
                </li>
            @endif

            @if(isModuleActive('Survey'))
                <li>
                    <a href="{{route('survey.student_survey')}}"
                       class=" d-flex align-items-center  {{ routeIs('survey.student_survey')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/compact/')}}/img/svg/purchase_history.svg" alt="">
                        </div>
                        <span>{{__('survey.Survey')}}</span>
                    </a>
                </li>
            @endif

            @if(isModuleActive('Chat'))
                <li>
                    <a class=" d-flex justify-content-between {{ routeIs('chat.index')  ? 'active' : '' }}"
                       data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                       aria-controls="collapseExample">

                        <span>@lang('chat.chat') </span>
                        <i class="fa fa-chevron-down text-black"></i>
                    </a>
                    <ul class="collapse chat-menu-ul" id="collapseExample">
                        <li>
                            <a class="chat-submenu" href="{{ route('chat.index') }}">{{ __('chat.chat_box') }}</a>
                        </li>

                        <li>
                            <a class="chat-submenu"
                               href="{{ route('chat.invitation') }}">{{ __('chat.invitation') }}</a>
                        </li>

                        <li>
                            <a class="chat-submenu"
                               href="{{ route('chat.blocked.users') }}">{{ __('chat.blocked_user') }}</a>
                        </li>
                    </ul>
                </li>
            @endif

            <li>
                <a href="{{route('myNotificationSetup')}}"
                   class=" d-flex align-items-center  {{ routeIs('myNotificationSetup')  ? 'active' : '' }}">
                    <div class="menu_icon">
                        <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/purchase_history.svg" alt="">
                    </div>
                    <span>{{ __('setting.Notification Setup') }}</span>
                </a>
            </li>
            @if(isModuleActive('BundleSubscription'))
            @endif
            @if (permissionCheck('myCertificate'))
                <li>
                    <a href="{{route('myCertificate')}}"
                       class=" d-flex align-items-center  {{ routeIs('myCertificate')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/purchase_history.svg" alt="">
                        </div>
                        <span>{{__('certificate.Certificate')}}</span>
                    </a>
                </li>
            @endif
            @if (isModuleActive('Assignment'))

                <li>
                    <a href="{{route('myAssignment')}}"
                       class=" d-flex align-items-center  {{ routeIs('myAssignment')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/purchase_history.svg" alt="">
                        </div>
                        <span>{{__('assignment.Assignment')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('myPurchases'))
                <li>
                    <a href="{{route('myPurchases')}}"
                       class=" d-flex align-items-center  {{ routeIs('myPurchases')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/purchase_history.svg" alt="">
                        </div>
                        <span>{{__('common.Purchase History')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('myProfile'))
                <li>
                    <a href="{{route('myProfile')}}"
                       class=" d-flex align-items-center {{ routeIs('myProfile')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/edit_pro.svg" alt="">
                        </div>
                        <span>{{__('frontendmanage.My Profile')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('myAccount'))
                <li>
                    <a href="{{route('myAccount')}}"
                       class=" d-flex align-items-center {{ routeIs('myAccount')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/account_setting.svg" alt="">
                        </div>
                        <span>{{__('frontend.Account Settings')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('deposit'))
                <li>
                    <a href="{{route('deposit')}}"
                       class=" d-flex align-items-center {{ routeIs('deposit')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/account_setting.svg" alt="">
                        </div>
                        <span>{{__('common.Deposit')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('logged.in.devices'))
                <li>
                    <a href="{{route('logged.in.devices')}}"
                       class=" d-flex align-items-center {{ routeIs('logged.in.devices')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/account_setting.svg" alt="">
                        </div>
                        <span>{{__('common.Logged In Devices')}}</span>
                    </a>
                </li>
            @endif
            @if (permissionCheck('referral'))
                <li>
                    <a href="{{route('referral')}}"
                       class=" d-flex align-items-center {{ routeIs('referral')  ? 'active' : '' }}">
                        <div class="menu_icon">
                            <img src="{{asset('/public/frontend/infixlmstheme/')}}/img/svg/account_setting.svg" alt="">
                        </div>
                        <span>{{__('common.Referral')}}</span>
                    </a>
                </li>
            @endif
            @if(isModuleActive('Subscription'))
                @if(isSubscribe())
                    <li>
                        <a href="{{route('subscriptionCourses')}}"
                           class=" d-flex align-items-center {{ routeIs('subscriptionCourses')  ? 'active' : '' }}">

                            <span>{{__('subscription.Subscription')}}</span>
                        </a>
                    </li>
                @endif
            @endif
            @if(isModuleActive('Affiliate') && permissionCheck('affiliate.my_affiliate.index'))
                <li>
                    <a href="{{route('affiliate.my_affiliate.index')}}"
                       class=" d-flex align-items-center {{ routeIs('affiliate.my_affiliate.index')  ? 'active' : '' }}">
                        <span>{{__('affiliate.My Affiliate')}}</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</nav>
<!-- sidebar part end -->
