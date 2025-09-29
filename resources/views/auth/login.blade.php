<form method="POST" action="{{ route('login') }}">
    @csrf
    <label>Email:</label>
    <input type="email" name="vEmail" required value="{{ old('vEmail') }}">
    @error('vEmail')
        <div style="color: red;">{{ $message }}</div>
    @enderror
    
    <label>Contraseña:</label>
    <input type="password" name="vPassword" required>
    @error('vPassword')
        <div style="color: red;">{{ $message }}</div>
    @enderror

    <label>
        <input type="checkbox" name="remember"> Recordarme
    </label>
    
    <button type="submit">Ingresar</button>
</form>
