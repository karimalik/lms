<div>
    <div class="main_content_iner">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="purchase_history_wrapper">
                        <div class="row">
                            <div class="col-12">
                                <div class="section__title3 mb_40">
                                    <h3 class="mb-0">{{__('payment.Fund Deposit')}}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <form action="{{route('depositSelectOption')}}" method="post">
                                    <div class="single_totl_warp col-lg-12 pl-0">
                                        @csrf

                                        <h3 class="font_18 mb-2">{{__('payment.Deposit Amount')}}
                                            ({{Settings('currency_symbol') ??'৳'}})
                                            <span
                                                class="text-danger">*</span></h3>
                                        <div class="input-group mb-3 input-group-lg">

                                            <input
                                                placeholder="{{Settings('currency_symbol') ??'৳'}}"
                                                name="deposit_amount" type="number" step="any"
                                                min="1" value="{{!empty($amount)?$amount:''}}"
                                                class="primary_input col-md-6"
                                                required>

                                            <div class="input-group-prepend">
                                                <button type="submit" style="margin-bottom: 30px;"
                                                        class="theme_btn btn-sm  small_btn2">{{__('payment.Save Info')}} </button>

                                            </div>
                                            <strong
                                                class="text-danger">{{$errors->first('deposit_amount')}}</strong>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @if(!empty($amount))
                            <div class="row">
                                <div class="col-12">
                                    <div class="">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="section__title3 mb_40">
                                                    <h3 class="mb-0">{{__('common.Select')}} {{__('payment.Payment Method')}}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">

                                                <div class="deposit_lists_wrapper mb-50">
                                                    @if(isset($methods))
                                                        @foreach($methods as $key=>$gateway)
                                                            @php
                                                                if (!paymentGateWayCredentialsEmptyCheck($gateway->method)){
                                                                continue;
                                                                }
                                                            @endphp
                                                            <div
                                                                class="single_deposite {{$gateway->method=="Bank Payment"?'p-0 border-0':''}}">

                                                                @if($gateway->method=="Stripe")
                                                                    <form action="{{route('depositSubmit')}}"
                                                                          method="post">
                                                                        @csrf
                                                                        <input type="hidden" name="method"
                                                                               value="{{$gateway->method}}">
                                                                        <input type="hidden" name="deposit_amount"
                                                                               value="{{$amount}}">
                                                                        <!-- single_deposite_item  -->
                                                                        <button type="submit" class="">
                                                                            <img
                                                                                src="{{asset($gateway->logo)}}"
                                                                                alt="">
                                                                        </button>
                                                                        @csrf
                                                                        <script
                                                                            src="https://checkout.stripe.com/checkout.js"
                                                                            class="stripe-button"
                                                                            data-key="{{ getPaymentEnv('STRIPE_KEY') }}"
                                                                            data-name="Stripe Payment"
                                                                            data-image="{{asset(Settings('favicon') )}}"
                                                                            data-locale="auto"
                                                                            data-currency="usd">
                                                                        </script>


                                                                    </form>
                                                                @elseif($gateway->method=="MercadoPago")

                                                                    <div class="">

                                                                        <a href="#" data-toggle="modal"
                                                                           data-target="#MakePaymentFromCreditMercadoPago"
                                                                           class=" ">
                                                                            <img class=" w-100" style="    padding: 0;
                                                                    margin-top: -2px;"
                                                                                 src="{{asset($gateway->logo)}}"
                                                                                 alt="">
                                                                        </a>
                                                                    </div>


                                                                    <div class="modal fade "
                                                                         id="MakePaymentFromCreditMercadoPago"
                                                                         tabindex="-1"
                                                                         role="dialog"
                                                                         aria-labelledby="exampleModalLabel"
                                                                         aria-hidden="true">
                                                                        <div class="modal-dialog modal-lg"
                                                                             role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="">MercadoPago</h5>
                                                                                </div>


                                                                                <div class="modal-body">
                                                                                    <div class="row">
                                                                                        @php
                                                                                            $total_amount =$amount;
                                                                                            $route =route('depositSubmit');
                                                                                            $payment_type ='Deposit'
                                                                                        @endphp
                                                                                        <div class="col-md-12">
                                                                                            @include('mercadopago::partials._checkout',compact('total_amount','payment_type'))
                                                                                        </div>
                                                                                    </div>


                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                @elseif($gateway->method=="RazorPay")

                                                                    @csrf

                                                                    <div class="single_deposite_item">

                                                                        <div class="deposite_button text-center">
                                                                            <form action="{{ route('depositSubmit') }}"
                                                                                  method="POST">
                                                                                <input type="hidden" name="method"
                                                                                       value="{{$gateway->method}}">
                                                                                <input type="hidden"
                                                                                       name="deposit_amount"
                                                                                       value="{{$amount}}">
                                                                                <button type="button"
                                                                                        class="">
                                                                                    <img class="submitBtn"
                                                                                         src="{{asset($gateway->logo)}}"
                                                                                         alt="">
                                                                                </button>
                                                                                @csrf
                                                                                <script
                                                                                    src="https://checkout.razorpay.com/v1/checkout.js"
                                                                                    data-key="{{ getPaymentEnv('RAZOR_KEY') }}"
                                                                                    data-amount="{{ convertCurrency(Settings('currency_code') ??'BDT', 'INR', $amount)*100}}"
                                                                                    data-name="{{str_replace('_', ' ',config('app.name') ) }}"
                                                                                    data-description="Cart Payment"
                                                                                    data-image="{{asset(Settings('favicon') )}}"
                                                                                    data-prefill.name="{{ @Auth::user()->username }}"
                                                                                    data-prefill.email="{{ @Auth::user()->email }}"
                                                                                    data-theme.color="#ff7529">
                                                                                </script>
                                                                            </form>
                                                                        </div>
                                                                    </div>

                                                                @elseif($gateway->method=="PayPal")

                                                                    <form action="{{route('depositSubmit')}}"
                                                                          method="post">
                                                                        @csrf
                                                                        <input type="hidden" name="method"
                                                                               value="{{$gateway->method}}">
                                                                        <input type="hidden" name="deposit_amount"
                                                                               value="{{$amount}}">
                                                                        <button type="submit" class="">
                                                                            <img class=""
                                                                                 src="{{asset($gateway->logo)}}"
                                                                                 alt="">
                                                                        </button>

                                                                    </form>
                                                                @elseif($gateway->method=="PayTM")

                                                                    <form action="{{route('depositSubmit')}}"
                                                                          method="post">
                                                                        @csrf
                                                                        <input type="hidden" name="method"
                                                                               value="{{$gateway->method}}">
                                                                        <input type="hidden" name="deposit_amount"
                                                                               value="{{$amount}}">
                                                                        <button type="submit" class="">
                                                                            <img
                                                                                src="{{asset($gateway->logo)}}"
                                                                                alt="">
                                                                        </button>

                                                                    </form>

                                                                @elseif($gateway->method=="PayStack")

                                                                    <form action="{{route('depositSubmit')}}"
                                                                          method="post">
                                                                        @csrf

                                                                        <input type="hidden" name="email"
                                                                               value="{{ @Auth::user()->email}}"> {{-- required --}}
                                                                        <input type="hidden" name="orderID"
                                                                               value="{{md5(uniqid(rand(), true))}}">
                                                                        <input type="hidden" name="amount"
                                                                               value="{{$amount*100}}">
                                                                        <input type="hidden" name="deposit_amount"
                                                                               value="{{$amount*100}}">

                                                                        <input type="hidden" name="currency"
                                                                               value="{{Settings('currency_code')}}">
                                                                        <input type="hidden" name="metadata"
                                                                               value="{{ json_encode($array = ['type' => 'Deposit',]) }}">
                                                                        <input type="hidden" name="reference"
                                                                               value="{{ Paystack::genTranxRef() }}"> {{-- required --}}

                                                                        <input type="hidden" name="method"
                                                                               value="{{$gateway->method}}">

                                                                        <button type="submit" class="">
                                                                            <img
                                                                                src="{{asset($gateway->logo)}}"
                                                                                alt="">
                                                                        </button>

                                                                    </form>
                                                                @elseif($gateway->method=="Bkash")

                                                                    <form action="{{route('depositSubmit')}}"
                                                                          method="post">
                                                                        @csrf
                                                                        @if(env('IS_BKASH_LOCALHOST'))
                                                                            <script id="myScript"
                                                                                    src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
                                                                        @else
                                                                            <script id="myScript"
                                                                                    src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
                                                                        @endif

                                                                        <input type="hidden" name="method"
                                                                               value="{{$gateway->method}}">
                                                                        <input type="hidden" name="deposit_amount"
                                                                               value="{{$amount}}">
                                                                        <button type="button" class="" id="bKash_button"
                                                                                onclick="BkashPayment()">
                                                                            <img class=""
                                                                                 src="{{asset($gateway->logo)}}"
                                                                                 alt="">
                                                                        </button>
                                                                        @php
                                                                            $type ='Deposit';
                                                                        @endphp
                                                                        @include('bkash::bkash-script',compact('type','amount'))

                                                                    </form>
                                                                @elseif($gateway->method=="Bank Payment")
                                                                    <form class="w-100" action="" method="post">
                                                                        @csrf

                                                                        <a href="#" data-toggle="modal"
                                                                           data-target="#bankModel"
                                                                           class="payment_btn_text2 w-100">
                                                                            {{$gateway->method}}
                                                                        </a>
                                                                    </form>
                                                                @else

                                                                    <form action="{{route('depositSubmit')}}"
                                                                          method="post">
                                                                        @csrf
                                                                        <input type="hidden" name="method"
                                                                               value="{{$gateway->method}}">
                                                                        <input type="hidden" name="deposit_amount"
                                                                               value="{{$amount}}">
                                                                        <button type="submit" class="">
                                                                            <img
                                                                                src="{{asset($gateway->logo)}}"
                                                                                alt="">
                                                                        </button>

                                                                    </form>

                                                                @endif


                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(count($records)!=0)
        <div class="main_content_iner">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="purchase_history_wrapper">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section__title3 mb_40">
                                        <h3 class="mb-0">{{__('payment.Deposit history')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="table-responsive">
                                        <table class="table custom_table3 mb-0">
                                            <thead>
                                            <tr>
                                                <th scope="col">{{__('common.SL')}}</th>
                                                <th scope="col">{{__('common.Date')}}</th>
                                                <th scope="col">{{__('payment.Amount')}}</th>
                                                <th scope="col">{{__('payment.Method')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($records))
                                                @foreach ($records as $key=>$record)
                                                    <tr>
                                                        <td>{{@$key+1}}</td>
                                                        <td>{{ showDate($record->created_at) }}</td>
                                                        <td>{{@$record->amount}}   </td>
                                                        <td>{{@$record->method}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        <div class="pt-3">
                                            {{ $records->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade " id="bankModel"
         tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg"
             role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">Bank
                        Payment </h5>
                </div>
                <form name="bank_payment"
                      enctype="multipart/form-data"
                      action="{{route('depositSubmit')}} "
                      class="single_account-form"
                      method="POST">
                    <div class="modal-body">
                        @csrf

                        <input type="hidden"
                               name="method"
                               value="Bank Payment">
                        <div class="row">
                            <div
                                class="col-xl-6 col-md-6">
                                <label for="name"
                                       class="mb-2">@lang('setting.Bank Name')
                                    <span>*</span></label>
                                <input type="text"
                                       required
                                       class="primary_input4 mb_20"
                                       placeholder="Bank Name"
                                       name="bank_name"
                                       value="{{@old('bank_name')}}">
                                <span
                                    class="invalid-feedback"
                                    role="alert"
                                    id="bank_name"></span>
                            </div>
                            <div
                                class="col-xl-6 col-md-6">
                                <label for="name"
                                       class="mb-2">@lang('setting.Branch Name')
                                    <span>*</span></label>
                                <input type="text"
                                       required
                                       name="branch_name"
                                       class="primary_input4 mb_20"
                                       placeholder="Name of account owner"
                                       value="{{@old('branch_name')}}">
                                <span
                                    class="invalid-feedback"
                                    role="alert"
                                    id="owner_name"></span>
                            </div>
                        </div>
                        <div class="row mb-20">

                            <div
                                class="col-xl-6 col-md-6">
                                <label for="name"
                                       class="mb-2">@lang('setting.Account Number')
                                    <span>*</span></label>
                                <input type="text"
                                       required
                                       class="primary_input4 mb_20"
                                       placeholder="Account number"
                                       name="account_number"
                                       value="{{@old('account_number')}}">
                                <span
                                    class="invalid-feedback"
                                    role="alert"
                                    id="account_number"></span>
                            </div>
                            <div
                                class="col-xl-6 col-md-6">
                                <label for="name"
                                       class="mb-2">@lang('setting.Account Holder')
                                    <span>*</span></label>
                                <input type="text"
                                       required
                                       name="account_holder"
                                       class="primary_input4 mb_20"
                                       placeholder="Account Holder"
                                       value="{{@old('account_holder')}}">
                                <span
                                    class="invalid-feedback"
                                    role="alert"
                                    id="account_holder"></span>
                            </div>
                            <input type="hidden"
                                   name="deposit_amount"
                                   value="{{$amount}}">


                        </div>

                        <div class="row  mb-20">


                            <div
                                class="col-xl-6 col-md-12">
                                <label for="name"
                                       class="mb-2">@lang('setting.Account Type')
                                    <span>*</span></label>
                                <select class="theme_select wide update-select-arrow" name="type" required
                                        id="type"
                                        style="margin-top: -10px;">
                                    <option
                                        data-display="{{__('common.Select')}}  {{__('setting.Account Type')}}"
                                        value="">{{__('common.Select')}} {{__('setting.Account Type')}}</option>
                                    <option
                                        value="Current Account" {{(env('ACCOUNT_TYPE')? env('ACCOUNT_TYPE') : '')=='Current Account'?'selected':''}}>
                                        Current Account
                                    </option>

                                    <option
                                        value="Savings Account" {{(env('ACCOUNT_TYPE')? env('ACCOUNT_TYPE') : '')=='Savings Account'?'selected':''}}>
                                        Savings Account
                                    </option>
                                    <option
                                        value="Salary Account" {{(env('ACCOUNT_TYPE')? env('ACCOUNT_TYPE') : '')=='Salary Account'?'selected':''}}>
                                        Salary Account
                                    </option>
                                    <option
                                        value="Fixed Deposit" {{(env('ACCOUNT_TYPE')? env('ACCOUNT_TYPE') : '')=='Fixed Deposit'?'selected':''}}>
                                        Fixed Deposit
                                    </option>

                                </select>
                            </div>
                            <div
                                class="col-xl-6 col-md-12">
                                <label for="name"
                                       class="mb-2">Cheque
                                    Slip
                                    <span>*</span></label>
                                <input type="file"
                                       required
                                       name="image"
                                       class="primary_input4 mb_20">
                                <span
                                    class="invalid-feedback"
                                    role="alert"
                                    id="amount_validation"></span>
                            </div>
                        </div>

                        <fieldset class="mt-3">
                            <legend>Bank Account Info
                            </legend>
                            <table
                                class="table table-bordered">

                                <tr>
                                    <td>@lang('setting.Bank Name')</td>
                                    <td>{{env('BANK_NAME')}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('setting.Branch Name')</td>
                                    <td>{{env('BRANCH_NAME')}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('setting.Account Type')</td>
                                    <td>{{env('ACCOUNT_TYPE')}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('setting.Account Number')</td>
                                    <td>{{env('ACCOUNT_NUMBER')}}</td>
                                </tr>

                                <tr>
                                    <td>@lang('setting.Account Holder')</td>
                                    <td>{{env('ACCOUNT_HOLDER')}}</td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                    <div
                        class="modal-footer d-flex justify-content-between">
                        <button type="button"
                                class=" theme_line_btn "
                                data-dismiss="modal">@lang('common.Cancel')</button>
                        <button class="  theme_btn"
                                type="submit">@lang('payment.Payment')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
