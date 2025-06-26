{{-- platform/plugins/ecommerce/resources/views/customers/auth/verify-otp-box.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <title>OTP Verification</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- Botble core + Ecommerce plugin CSS --}}
  <link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/base.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/core/plugins/ecommerce/css/ecommerce.css') }}">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      background: #f8f9fa;
    }
    .otp-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      padding: 1rem;
    }
    .otp-card {
      width: 100%;
      max-width: 400px;
      border: 1px solid #dee2e6;
      border-radius: .25rem;
      background: #fff;
      box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
      overflow: hidden;
    }
    .otp-card .card-header {
      background-color: #007bff;
      color: #fff;
      padding: 1rem;
      text-align: center;
      font-size: 1.25rem;
      font-weight: 500;
    }
    .otp-card .card-body {
      padding: 1.5rem;
    }
    .otp-card .form-control {
      font-size: 1rem;
      padding: .5rem;
    }
    /* Custom blue button */
    .btn-blue {
      background-color: #007bff;
      border-color: #007bff;
      color: #fff;
      width: 100%;
      padding: .75rem;
      font-size: 1rem;
      margin-top: 1rem;
      border-radius: .25rem;
      cursor: pointer;
    }
    .btn-blue:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }
    /* Resend link style */
    .resend-link {
      display: block;
      text-align: center;
      margin-top: .5rem;
      color: #007bff;
      text-decoration: none;
    }
    .resend-link:hover {
      text-decoration: underline;
    }
    .otp-card .alert {
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <div class="otp-wrapper">
    <div class="otp-card card">
      <div class="card-header">
        Verify Your Email
      </div>
      <div class="card-body">
        <p class="mb-4 text-center">
          An OTP has been sent to<br>
          <strong>{{ $email }}</strong>
        </p>

        @if ($errors->any())
          <div class="alert alert-danger">
            {{ $errors->first() }}
          </div>
        @endif

        <form action="{{ route('customer.otp.verify') }}" method="POST">
          @csrf
          <input type="hidden" name="email" value="{{ $email }}">

          <div class="form-group mb-3">
            <label for="otp">OTP Code</label>
            <input
              type="text"
              id="otp"
              name="otp"
              class="form-control"
              required
              maxlength="6"
              placeholder="Enter 6-digit code"
            >
          </div>

          <button type="submit" class="btn-blue">
            Verify OTP
          </button>
        </form>

        {{-- Optional: Resend OTP link --}}
        {{-- <a href="{{ route('customer.otp.resend', ['email' => $email]) }}" class="resend-link">
          Didn’t receive the code? Resend OTP
        </a> --}}
      </div>
    </div>
  </div>
</body>
</html>
