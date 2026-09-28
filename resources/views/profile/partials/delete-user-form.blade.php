<section>
    <h2 class="h5 fw-semibold text-danger mb-1">Delete account</h2>
    <p class="text-muted small mb-4">
        Once your account is deleted, you can no longer log in. Records you registered stay in the system.
    </p>

    <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
        Delete account
    </button>

    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content rounded-4">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionLabel">Are you sure you want to delete your account?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="small text-muted">Please enter your password to confirm.</p>
                    <label for="delete_password" class="visually-hidden">Password</label>
                    <input id="delete_password" name="password" type="password" placeholder="Password"
                           class="form-control @error('password', 'userDeletion') is-invalid @enderror">
                    @error('password', 'userDeletion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-pink-soft rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Delete account</button>
                </div>
            </form>
        </div>
    </div>
</section>

@if($errors->userDeletion->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('confirmUserDeletion')).show();
            });
        </script>
    @endpush
@endif
