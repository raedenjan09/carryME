@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">{{ __('Create Account') }}</h3>

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="preview" src="{{ asset('images/default-avatar.png') }}" 
                                     class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                                <label for="profile_picture" class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-2" 
                                       style="cursor: pointer;">
                                    <i class="bi bi-camera-fill text-white"></i>
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" class="d-none" 
                                       accept="image/*" onchange="previewImage(this)">
                            </div>
                            @error('profile_picture')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            <label for="name">{{ __('Full Name') }}</label>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email">
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone') }}" autocomplete="tel">
                            <label for="phone">{{ __('Phone Number') }}</label>
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <textarea id="address" class="form-control @error('address') is-invalid @enderror" 
                                     name="address" style="height: 100px">{{ old('address') }}</textarea>
                            <label for="address">{{ __('Address') }}</label>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="new-password">
                            <label for="password">{{ __('Password') }}</label>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <input id="password-confirm" type="password" class="form-control" 
                                   name="password_confirmation" required autocomplete="new-password">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            {{ __('Register') }}
                        </button>

                        <p class="text-center mb-0">
                            {{ __('Already have an account?') }} 
                            <a href="{{ route('login') }}" class="text-decoration-none">{{ __('Login here') }}</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
