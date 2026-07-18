<div class="d-flex align-items-center gap-4 mb-3">
    @php
        $avatarUrl = auth()->user()->avatar
            ? asset('storage/' . auth()->user()->avatar)
            : asset('dashui/assets/images/avatar/' . (auth()->user()->role === 'admin' ? 'avatar-8.jpg' : 'avatar-5.jpg'));
    @endphp

    <img id="avatar-preview"
         src="{{ $avatarUrl }}"
         alt="avatar"
         class="rounded-circle"
         style="width: 88px; height: 88px; object-fit: cover; border: 3px solid #f1f5f9;">

    <div>
        <form method="post" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" id="avatar-form">
            @csrf
            <label for="avatar" class="btn btn-primary btn-sm mb-0">
                Pilih Foto
            </label>
            <input type="file" name="avatar" id="avatar" accept="image/png, image/jpeg" class="d-none">
        </form>

        @if (auth()->user()->avatar)
            <form method="post" action="{{ route('profile.avatar.destroy') }}" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-link btn-sm text-danger p-0 ms-3 text-decoration-none">
                    Hapus Foto
                </button>
            </form>
        @endif

        <p class="text-muted small mt-2 mb-0">JPG atau PNG, maksimal 2MB.</p>
    </div>
</div>

@error('avatar')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

@if (session('status') === 'avatar-updated')
    <div class="alert alert-success py-2 mb-0">Foto berhasil diperbarui.</div>
@elseif (session('status') === 'avatar-deleted')
    <div class="alert alert-success py-2 mb-0">Foto berhasil dihapus.</div>
@endif


<script>
    document.getElementById('avatar').addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;

        try {
            const preview = document.getElementById('avatar-preview');
            preview.src = window.URL.createObjectURL(this.files[0]);
        } catch (e) {
            console.warn('Preview gagal ditampilkan, tapi upload tetap lanjut:', e);
        }

        document.getElementById('avatar-form').requestSubmit();
    });
</script>