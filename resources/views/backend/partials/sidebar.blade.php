<!-- sidebar part here -->
<nav id="sidebar" class="sidebar ">

    <div class="sidebar-header update_sidebar">
        <a class="large_logo" href="{{url('/')}}">
            <img src="{{getLogoImage(Settings('logo') )}}" alt="">
        </a>
        <a class="mini_logo" href="{{url('/')}}">
            <img src="{{getLogoImage(Settings('logo') )}}" alt="">
        </a>
        <a id="close_sidebar" class="d-lg-none">
            <i class="ti-close"></i>
        </a>
    </div>
    <ul id="sidebar_menu">

        @if (isModuleActive('LmsSaas') && SaasDomain()!='main' && !hasActiveSaasPlan() )
            <li>
                <a href="#" class="has-arrow" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-university"></span>
                    </div>
                    <div class="nav_title">
                        <span>{{ __('saas.Saas Management') }}</span>
                    </div>
                </a>

                <ul>
                    <li>
                        <a href="{{route('saas.myPlan')}}">{{__('saas.My Plan')}}</a>
                    </li>
                </ul>
            </li>
        @else
            @if (permissionCheck('dashboard'))
                <li>
                    <a class="active" href="{{url('/dashboard')}}" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-th"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{__('common.Dashboard')}}</span>
                        </div>
                    </a>
                </li>
            @endif


            @if (permissionCheck('students'))
                @include('studentsetting::menu')
            @endif

            @if (permissionCheck('instructors'))
                @include('systemsetting::menu')
            @endif

            @if(isModuleActive("Chat"))
                @include('chat::menu')
            @endif

            @if(isModuleActive("WhatsappSupport"))
                @include('whatsappsupport::menu')
            @endif
            @if(isModuleActive("HumanResource"))
                @include('humanresource::menu')
            @endif
            @if(isModuleActive("SkillAndPathway"))
                @include('skillandpathway::menu')
            @endif
            @if (permissionCheck('courses'))
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-cubes"></span>
                        </div>
                        <div class="nav_title">
                            <span> {{ __('courses.Courses') }}</span>
                        </div>
                    </a>
                    <ul>
                        @if (permissionCheck('course.category'))
                            <li><a href="{{ route('course.category') }}">{{ __('courses.Categories') }}</a></li>
                        @endif

                        @if(isModuleActive('Org') && permissionCheck('org.material'))
                            <li><a href="{{ route('org.material') }}">{{ __('org.Material Source') }}
                                    @if(env('APP_SYNC'))
                                        <span class="demo_addons_sub">Addon</span>
                                    @endif
                                </a></li>
                        @endif

                        @if (permissionCheck('course-level.index'))
                            <li><a href="{{ route('course-level.index') }}">{{ __('courses.Course Level') }}</a></li>
                        @endif

                        @if (permissionCheck('getAllCourse'))
                            <li>
                                <a href="{{ route('getAllCourse') }}">{{ __('courses.All') }} {{ __('courses.Courses') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('getActiveCourse'))
                            <li>
                                <a href="{{ route('getActiveCourse') }}">{{ __('courses.Active') }} {{ __('courses.Courses') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('getPendingCourse'))
                            <li>
                                <a href="{{ route('getPendingCourse') }}">{{ __('courses.Pending') }} {{ __('courses.Courses') }}</a>
                            </li>
                        @endif
                        @if(isModuleActive('Assignment'))
                            <li>
                                <a href="{{ route('courseAssignmentList') }}">
                                    <span>
                                    {{__('assignment.Assignment')}}

                                    </span>

                                    @if(env('APP_SYNC'))
                                        <span class="demo_addons_sub">Addon</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if(isModuleActive('CourseOffer') &&  permissionCheck('CourseOffer'))
                            <li>
                                <a href="{{ route('courseOffer') }}">{{__('frontendmanage.Course Offer')}}
                                    @if(env('APP_SYNC'))
                                        <span class="demo_addons_sub">Addon</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if (Settings('frontend_active_theme')=='compact')
                            <li>
                                <a href="{{ route('frontend.showCourseSettings') }}"> {{ __('frontendmanage.Course Setting') }}</a>
                            </li>
                        @endif

                        @if (permissionCheck('course.courseStatistics'))
                            <li>
                                <a href="{{ route('course.courseStatistics') }}"> {{__('courses.Course Statistics')}}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(isModuleActive('OrgSubscription') && permissionCheck('Orgsubscription'))
                @include('orgsubscription::menu')
            @endif






            @if (permissionCheck('quiz'))
                @include('quiz::menu')
            @endif
            @if (permissionCheck('coupons'))
                @include('coupons::menu')
            @endif

            @if(isModuleActive("Homework") && permissionCheck('homework_list'))
                @include('homework::menu')
            @endif

            @if(isModuleActive("Affiliate") && permissionCheck('affiliate'))
                @include('affiliate::menu')
            @endif

            @if(isModuleActive("Survey") && permissionCheck('survey'))
                @include('survey::menu')
            @endif

            @if (permissionCheck('communications'))
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-comments"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{__('communication.Communication')}}</span>
                        </div>
                    </a>
                    <ul>
                        @if (permissionCheck('communication.PrivateMessage'))
                            <li>
                                <a href="{{ route('communication.PrivateMessage') }}">{{__('communication.Private Messages')}}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            @if (permissionCheck('payments'))
                @include('payment::menu')
            @endif

            @if (permissionCheck('reports'))
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-calculator"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{__('setting.Reports')}}</span>
                        </div>
                    </a>
                    <ul>
                        @if (permissionCheck('admin.reveuneList'))
                            <li>
                                <a href="{{ route('admin.reveuneList') }}">{{__('courses.Admin Revenue')}}</a>
                            </li>
                        @endif
                        @if (permissionCheck('admin.reveuneListInstructor'))
                            <li>
                                <a href="{{ route('admin.reveuneListInstructor') }}">{{__('instructor.Instructors')}} {{__('payment.Revenue')}}</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif

            @if (permissionCheck('certificate.index'))
                @include('certificate::menu')
            @endif




            @if (permissionCheck('frontend_CMS'))
                @include('frontendmanage::menu')
            @endif


            @if (permissionCheck('zoom'))
                @include('zoom::menu')
            @endif


            @if(isModuleActive("BBB"))
                @if (permissionCheck('bbb'))
                    @include('bbb::menu')
                @endif
            @endif

            @if(isModuleActive("Jitsi"))
                @if (permissionCheck('Jitsi'))
                    @include('jitsi::menu')
                @endif
            @endif


            @if (permissionCheck('virtual-class'))
                @include('virtualclass::menu')
            @endif


            @if (permissionCheck('blog'))
                @include('blog::menu')
            @endif

            @if (isModuleActive("Group") && permissionCheck('groups'))
                @include('group::menu')
            @endif

            @if (isModuleActive("Catalogue") && permissionCheck('catalog'))
                @include('catalogue::menu')
            @endif

            @if(isModuleActive('Subscription') && permissionCheck('Subscription'))
                @include('subscription::menu')
            @endif



            @if(isModuleActive('BundleSubscription'))
                @if (permissionCheck('bundle.subscription'))
                    @include('bundlesubscription::backend.menu')
                @endif
            @endif
            @if(isModuleActive("Communicate") && permissionCheck('communicate'))
                @include('communicate::menu')
            @endif

            @if(isModuleActive("Calendar"))
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-th"></span>
                        </div>
                        <div class="nav_title">
                            <span>Calendar</span>
                            @if(env('APP_SYNC'))
                                <span class="demo_addons">Addon</span>
                            @endif
                        </div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('calendar_show') }}">Calendar</a>
                        </li>
                    </ul>
                </li>
            @endif
            @include('newsletter::menu')

           
            @if(permissionCheck('notification_setup_list'))
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fa fa-bell"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{ __('setting.Notification') }}</span>
                        </div>
                    </a>
                    <ul>
                        @if (permissionCheck('notification_setup_list'))
                            <li>
                                <a href="{{ route('notification_setup_list') }}">{{ __('setting.Notification Setup') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('UserNotificationControll'))
                            <li>
                                <a href="{{ route('UserNotificationControll') }}">{{ __('common.User') }} {{ __('setting.Notification') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(isModuleActive("Tax"))
                @include('tax::menu')
            @endif

            @if(isModuleActive("AmazonS3") && permissionCheck('AwsS3Setting'))
                <li>
                    <a href="{{ route('AwsS3Setting') }}" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-cogs"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{ __('common.Aws S3 Setting') }}</span>
                            @if(env('APP_SYNC'))
                                <span class="demo_addons">Addon</span>
                            @endif
                        </div>
                    </a>
                </li>
            @endif


            @if(permissionCheck('setting.pushNotification'))
                <li>
                    <a href="{{ route('setting.pushNotification') }}" aria-expanded="false">
                        <div class="nav_icon_small">
                            <i class="far fa-bell"></i>
                        </div>
                        <div class="nav_title">
                        <span>
                            {{ __('setting.Push Notification') }}
                        </span>

                        </div>
                    </a>
                </li>
            @endif
            @if (!isModuleActive('LmsSaas') && permissionCheck('setting.utility'))
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="nav_title">
                            <span>{{ __('setting.Utility') }}</span>
                        </div>
                    </a>

                    <ul>
                        @if(permissionCheck('setting.maintenance'))
                            <li>
                                <a href="{{ route('setting.maintenance') }}">{{__('setting.Maintenance')}}</a>
                            </li>
                        @endif
                        @if(permissionCheck('setting.utilities'))
                            <li>
                                <a href="{{ route('setting.utilities') }}">{{__('setting.Utilities')}}</a>
                            </li>
                        @endif

                        @if(permissionCheck('ipBlock.index'))
                            <li>
                                <a href="{{ route('ipBlock.index') }}">{{ __('setting.IP Block') }}</a>
                            </li>
                        @endif

                        @if(permissionCheck('setting.geoLocation'))
                            <li>
                                <a href="{{ route('setting.geoLocation') }}">{{ __('setting.Geo Location') }}</a>
                            </li>
                        @endif

                        @if(permissionCheck('setting.error_log'))
                            <li>
                                <a href="{{ route('setting.error_log') }}">{{__('setting.Error Log')}}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(!isModuleActive("HumanResource"))
                @if (permissionCheck('user.manager'))
                    <li>
                        <a href="#" class="has-arrow" aria-expanded="false">
                            <div class="nav_icon_small">
                                <span class="fas fa-cogs"></span>
                            </div>
                            <div class="nav_title">
                                <span>{{ __('setting.User Manage') }}</span>
                            </div>
                        </a>

                        <ul>
                            @if (permissionCheck('staffs.index'))
                                <li>
                                    <a href="{{ route('staffs.index') }}"
                                       class="{{request()->is('hr/staffs') || request()->is('hr/staffs/*') ? 'active' : ''}}">{{ __('common.Staff') }}</a>
                                </li>
                            @endif

                            @if (permissionCheck('hr.department.index'))
                                <li>
                                    <a href="{{ route('hr.department.index') }}"
                                       class="{{request()->is('hr/department') || request()->is('hr/department/*') ? 'active' : ''}}">{{ __('leave.Department') }}</a>
                                </li>
                            @endif

                            @if (permissionCheck('permission.roles.index'))
                                <li>
                                    <a href="{{ route('permission.roles.index') }}"
                                       class="{{request()->is('hr/role-permission/*') ? 'active' : '/*'}}">{{ __('role.Role') }}</a>
                                </li>
                            @endif


                            @if (permissionCheck('staffs.index'))
                                <li>
                                    <a href="{{ route('staffs.settings') }}"
                                       class="{{ request()->is('hr/settings/*') ? 'active' : ''}}">{{ __('common.Settings') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            @endif
            @if (permissionCheck('settings'))
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-cogs"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{ __('setting.System Setting') }}</span>
                        </div>
                    </a>

                    <ul>
                        @if (permissionCheck('setting.activation'))
                            <li>
                                <a href="{{ route('setting.activation') }}">{{ __('setting.Activation') }}</a>
                            </li>
                        @endif



                        @if (permissionCheck('setting.general_settings'))
                            <li>
                                <a href="{{ route('setting.general_settings') }}">{{ __('setting.General Settings') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('setting.setCommission'))
                            <li>
                                <a href="{{ route('setting.setCommission') }}">{{__('setting.Commission')}}</a>
                            </li>
                        @endif
                        @if (permissionCheck('settings.student_setup'))
                            @if (Settings('frontend_active_theme')=='compact')

                                <li>
                                    <a href="{{ route('settings.student_setup') }}">{{__('setting.Student Setup')}}</a>
                                </li>
                            @endif
                        @endif
                        @if (permissionCheck('settings.instructor_setup'))
                            <li>
                                <a href="{{ route('settings.instructor_setup') }}">{{__('setting.Instructor Setup')}}</a>
                            </li>
                        @endif
                        @if (permissionCheck('setting.email_setup'))
                            <li>
                                <a href="{{ route('setting.email_setup') }}">{{ __('setting.Email Setup') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('paymentmethodsetting.payment_method_setting'))
                            <li>
                                <a href="{{ route('paymentmethodsetting.payment_method_setting') }}">{{ __('setting.Payment Method Settings') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('paymentmethodsetting.payment_method_setting'))
                            <li>
                                <a href="{{ route('api.setting') }}">{{ __('setting.Api Settings') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('vimeosetting.index'))
                            <li>
                                <a href="{{ route('vimeosetting.index') }}">{{ __('setting.Vimeo Configuration') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('vdocipher.setting'))
                            <li>
                                <a href="{{ route('vdocipher.setting') }}">{{ __('setting.VdoCipher Configuration') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('gdrive.setting'))
                            <li>
                                <a href="{{ route('gdrive.setting') }}">{{ __('setting.GDrive Configuration') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('setting.seo_setting'))
                            <li>
                                <a href="{{ route('setting.seo_setting') }}">{{ __('setting.Homepage SEO Setup') }}</a>
                            </li>
                        @endif
                        @if(permissionCheck('setting.preloader'))
                            <li>
                                <a href="{{ route('setting.preloader') }}">{{ __('setting.Preloader') }} {{__('setting.Setting')}}</a>
                            </li>
                        @endif
                        {{--
                         @if(!isModuleActive("HumanResource"))
                              @if (permissionCheck('permission.roles.index'))
                                  <li>
                                      <a href="{{ route('permission.roles.index') }}">{{ __('role.Instructor Role') }}</a>
                                  </li>
                              @endif

                              @if (permissionCheck('permission.roles.index'))
                                  <li>
                                      <a href="{{ route('permission.student-roles') }}">{{ __('role.Student Role') }}</a>
                                  </li>
                              @endif
                          @endif
                          --}}
                        @if (permissionCheck('EmailTemp'))
                            <li>
                                <a href="{{ route('EmailTemp') }}">{{ __('setting.Email Template') }}</a>
                            </li>
                        @endif
                        @if (permissionCheck('languages.index'))
                            <li>
                                <a href="{{ route('languages.index') }}">{{ __('common.Language') }}</a>
                            </li>
                        @endif

                        @if (permissionCheck('currencies.index'))
                            <li>
                                <a href="{{ route('currencies.index') }}">{{ __('common.Currency') }}</a>
                            </li>
                        @endif

                        @if (permissionCheck('timezone.index'))
                            <li>
                                <a href="{{ route('timezone.index') }}">{{ __('common.TimeZone') }}</a>
                            </li>
                        @endif


                        @if (!isModuleActive('LmsSaas'))

                            @if (permissionCheck('city.index'))
                                <li>
                                    <a href="{{ route('city.index') }}">{{ __('common.City') }}</a>
                                </li>
                            @endif

                            @if(permissionCheck('setting.cookieSetting'))
                                <li>
                                    <a href="{{ route('setting.cookieSetting') }}">{{__('setting.Cookies settings')}}</a>
                                </li>
                            @endif

                            @if(permissionCheck('setting.cacheSetting'))
                                <li>
                                    <a href="{{ route('setting.cacheSetting') }}">{{__('setting.Cache settings')}}</a>
                                </li>
                            @endif

                            @if(permissionCheck('setting.queueSetting'))
                                <li>
                                    <a href="{{ route('setting.queueSetting') }}">{{__('setting.Queue settings')}}</a>
                                </li>
                            @endif


                            @if(permissionCheck('cronJob.index'))
                                <li>
                                    <a href="{{ route('setting.cronJob') }}">{{__('setting.Cron Job')}}</a>
                                </li>
                            @endif

                        @endif

                        @if(permissionCheck('setting.captcha'))
                            <li>
                                <a href="{{ route('setting.captcha') }}">{{__('setting.Google Captcha')}}</a>
                            </li>
                        @endif





                        @if(permissionCheck('setting.socialLogin'))
                            <li>
                                <a href="{{ route('setting.socialLogin') }}">{{__('setting.Social Login')}}</a>
                            </li>
                        @endif


                    </ul>
                </li>
            @endif
            @if (isModuleActive('LmsSaas') && SaasDomain()!='main')
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-university"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{ __('saas.Saas Management') }}</span>
                        </div>
                    </a>

                    <ul>
                        <li>
                            <a href="{{route('saas.myPlan')}}">{{__('saas.My Plan')}}</a>
                        </li>
                    </ul>
                </li>

                @if (isModuleActive('SaasBranch') && permissionCheck('saasbranch.index'))
                    @include('saasbranch::menu')
                @endif
            @endif
            @if (!isModuleActive('LmsSaas') && permissionCheck('backup.index'))
                @include('backup::menu')
            @endif
        @endif


    </ul>

</nav>

