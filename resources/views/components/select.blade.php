<select name="{{ $name }}" id="{{ $id }}" 
    {{ $attributes->merge(['class' => 'form-control w-auto']) }} 
    {{ $statusSantri != 1 ? 'disabled' : '' }}>

    @if ($statusSantri == 1)
        <option value="-">-</option>
        @foreach ($options as $value => $label)
            <option value="{{ $label->id }}" {{ $selected == $label->id ? 'selected' : '' }}>
                {{ $label->nomor }}
            </option>
        @endforeach
    @else
        <option value="">
            {{
                match ((int) $statusSantri) { // Pastikan $statusSantri dalam bentuk integer
                    0 => 'BOYONG',
                    1 => 'MASIH ZIYADAH',
                    2 => 'KHATAM',
                    3 => 'KHOTIMIN',
                    default => 'STATUS TIDAK DIKETAHUI'
                }
            }}
        </option>
    @endif
</select>