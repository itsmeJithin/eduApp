@extends('layouts.plain_layout')

@section('content')
    <div class="d-flex justify-content-center flex-column full-height ">
        <img src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png"
             data-src-retina="assets/img/logo_2x.png" width="78" height="22">
        <h3>APTU makes it easy to enjoy what matters the most in your education</h3>
        <p>
            Create an APTU account.
        </p>
        <form id="form-register" class="p-t-15" role="form" method="POST" action="{{ route('saveUserDetails') }}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-default  @error('staff_name') has-error @enderror">
                        <label>Name</label>
                        <input type="text" name="staff_name" placeholder="John" class="form-control" required
                               autocomplete="name" value="{{ old('staff_name') }}" id="staff_name">
                    </div>
                    @error('staff_name')
                    <label id="staff_name_error" class="error" role="alert" for="staff_name">
                        {{ $message }}
                    </label>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-default  @error('staff_email') has-error @enderror">
                        <label>Email</label>
                        <input type="email" name="staff_email" placeholder="We will send loging details to you"
                               class="form-control" required
                               autocomplete="name" value="{{ old('staff_email') }}" id="staff_email">
                    </div>
                    @error('staff_email')
                    <label id="staff_email_error" class="error" role="alert" for="staff_email">
                        {{$message }}
                    </label>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-default  @error('staff_phone_number') has-error @enderror">
                        <label>Phone Number</label>
                        <input type="text" name="staff_phone_number" placeholder="This should be unique"
                               class="form-control" required
                               autocomplete="name" value="{{ old('staff_phone_number') }}" id="staff_phone_number">
                    </div>
                    @error('staff_phone_number')
                    <label id="staff_phone_error" class="error" role="alert" for="staff_phone_number">
                        {{$message }}
                    </label>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-default  @error('staff_password') has-error @enderror">
                        <label>Password</label>
                        <input type="password" name="staff_password" placeholder="Minimum 6 letters"
                               class="form-control" required
                               autocomplete="new-password" value="{{ old('staff_password') }}" id="staff_password">
                    </div>
                    @error('staff_password')
                    <label id="staff_password_error" class="error" role="alert" for="staff_password">
                        {{$message }}
                    </label>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-default  @error('confirm_password') has-error @enderror">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Same as password"
                               class="form-control" required
                               autocomplete="new-password" value="{{ old('confirm_password') }}" id="confirm_password">
                    </div>
                    @error('confirm_password')
                    <label id="confirm_password_error" class="error" role="alert" for="confirm_password">
                        {{$message }}
                    </label>
                    @enderror
                </div>
            </div>
            <div class="row m-t-10">
                <div class="col-lg-6">
                    <p><small>I agree to the <a href="#" class="text-info">Pages Terms</a> and <a href="#"
                                                                                                  class="text-info">Privacy</a>.</small>
                    </p>
                </div>
                <div class="col-lg-6 text-right">
                    <a href="#" class="text-info small">Help? Contact Support</a>
                </div>
            </div>
            <button aria-label="" class="btn btn-primary btn-cons m-t-10" type="submit">Create a new account</button>
        </form>
    </div>
@endsection
@section("footer-script")
    <script>
        $(function () {
            $('#form-register').validate()
        })
    </script>
@endsection
