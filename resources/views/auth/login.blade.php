@extends('layouts.auth', ['title' => 'Login'])

@section('content')
	<div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
		{{-- <div class="d-flex flex-center flex-column flex-lg-row-fluid"> --}}
			{{-- <div class="w-lg-500px p-10"> --}}
		<div class="d-lg-flex flex-center flex-column flex-lg-row-fluid">
			<div class="w-lg-500px p-lg-10 px-5">
				<form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="index.html" action="{{ route('login.process') }}" method="post">
					@csrf
					<div class="text-center mb-11">
						<h1 class="text-gray-900 fw-bolder mb-3">Sign In</h1>
					</div>

					@if ($errors->any())
						<div class="alert alert-danger">
							@foreach ($errors->all() as $error)
								<p>{{ $error }}</p>
							@endforeach
						</div>
					@endif

					<div class="fv-row mb-8">
						<input type="text" placeholder="Kode Karyawan / Email" name="login" id="login" autocomplete="off" class="form-control bg-transparent" autofocus
						value="{{ old('login') }}"
						/>
						<div
							class="fv-plugins-message-container invalid-feedback error-text login_error">
						</div>
					</div>
					<!--
					<div class="fv-row mb-3">
						<input type="password" placeholder="Password" name="password" id="password" autocomplete="off" class="form-control bg-transparent" />
						<div
							class="fv-plugins-message-container invalid-feedback error-text password_error">
						</div>
					</div>
					-->
					<div class="d-flex flex-column fv-row mb-3">									
						<div class="position-relative">
							<input
								class="form-control bg-transparent"
								type="password"
								placeholder="Password"
								name="password"
								id="password"
								autocomplete="off"
							/>
							<span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
								<i class="ki-outline ki-eye-slash fs-2"></i>
								<i class="ki-outline ki-eye fs-2 d-none"></i>
							</span>
						</div>
						<div class="fv-plugins-message-container invalid-feedback error-text password_error"></div>
					</div>
					<div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
						<div></div>
						<a href="{{ route('forgot-password') }}" class="link-primary">Forgot Password ?</a>
					</div>
					<div class="d-grid mb-10">
						{{-- <button type="submit" id="kt_sign_in_submit" class="btn btn-primary"> --}}
						<button type="submit" id="" class="btn btn-primary">
							<span class="indicator-label">Sign In</span>
							<span class="indicator-progress">Please wait... 
							<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
						</button>
					</div>
					<div class="text-gray-500 text-center fw-semibold fs-6 d-none">
						Not a Member yet? 
						<a href="#" class="link-primary">Sign up</a>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection