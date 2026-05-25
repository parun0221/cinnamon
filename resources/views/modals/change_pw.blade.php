<div class="modal fade" id="passwordForm" tabindex="-1" aria-labelledby="passwordFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordFormLabel">Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordChangeForm" method="POST" action="">
                    @csrf
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Password Sekarang</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password"
                            required>
                        <div id="currentPasswordError" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirmPassword"
                            name="new_password_confirmation" required>
                        <div id="confirmPasswordError" class="invalid-feedback"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    
</div>