<div class="">
    {{ $karyawan->kd_karyawan }}
</div>
<div class="symbol symbol-circle symbol-50px overflow-hidden">
    @if ($karyawan->foto !== null && $karyawan->foto !== 'user.png')
        <div class="symbol-label">
            <img 
                src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/{{ $karyawan->foto }}"
                alt="{{ $karyawan->nama }}" 
            />
        </div>
    @else
        <div class="symbol-label fs-3 {{ app(\App\Actions\GetThemeType::class)->handle('bg-light-? text-?', $user->nama) }}">
            {{ substr($user->nama, 0, 1) }}
        </div>
    @endif
</div>