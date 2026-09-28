<section>
    <h2 class="h5 fw-semibold mb-1">Profile information</h2>
    <p class="text-muted small mb-4">Update your account's name and email address.</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                   class="form-control @error('name') is-invalid @enderror" required autocomplete="name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                   class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="small mt-2 mb-0">
                    Your email address is unverified.
                    <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">
                        Click here to re-send the verification email.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="small text-success mt-1 mb-0">A new verification link has been sent to your email address.</p>
                @endif
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary rounded-pill px-4">Save</button>

            @if (session('status') === 'profile-updated')
                <span class="small text-success"><i class="bi bi-check-circle me-1"></i>Saved.</span>
            @endif
        </div>
    </form>
</section>
