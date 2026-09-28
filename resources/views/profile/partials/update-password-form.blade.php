<section>
    <h2 class="h5 fw-semibold mb-1">Update password</h2>
    <p class="text-muted small mb-4">Use a long, random password to keep your account secure.</p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label fw-semibold">Current password</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
            @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label fw-semibold">New password</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror">
            @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label fw-semibold">Confirm password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror">
            @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary rounded-pill px-4">Save</button>

            @if (session('status') === 'password-updated')
                <span class="small text-success"><i class="bi bi-check-circle me-1"></i>Saved.</span>
            @endif
        </div>
    </form>
</section>
