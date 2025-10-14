@csrf

<div class="mb-3">
    <label for="name" class="form-label">Nama Lengkap</label>
    <input type="text" 
           class="form-control @error('name') is-invalid @enderror" 
           id="name" 
           name="name" 
           value="{{ old('name', $user->name ?? '') }}" 
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" 
           class="form-control @error('email') is-invalid @enderror" 
           id="email" 
           name="email" 
           value="{{ old('email', $user->email ?? '') }}" 
           required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="password" class="form-label">
        Password
        @if(isset($update) && $update)
            <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>
        @endif
    </label>
    <input type="password" 
           class="form-control @error('password') is-invalid @enderror" 
           id="password" 
           name="password"
           {{ (!isset($update) || !$update) ? 'required' : '' }}>
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
    <input type="password" 
           class="form-control @error('password_confirmation') is-invalid @enderror" 
           id="password_confirmation" 
           name="password_confirmation">
    @error('password_confirmation')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-dark">
        @if(isset($update) && $update)
            UPDATE
        @else
            SIMPAN
        @endif
    </button>
    
    <a href="{{ route('user.index') }}" class="btn btn-secondary ms-2">Kembali</a>
</div>