<select name="{{ $name }}" id="{{ $id }}" {{ $attributes->merge(['class' => 'form-control w-auto']) }}>
    <option value="-">-</option>
    @foreach ($options as $value => $label)
        <option value="{{ $label->id }}" {{ $selected == $label->id ? 'selected' : '' }}>
            {{ $label->nomor }}
        </option>
    @endforeach
</select>