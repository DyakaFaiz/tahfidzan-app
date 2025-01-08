<form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel">
    <button type="submit">Submit</button>
</form>

@if (session('error'))
    <script>
        console.log('{{ session('error') }}');
    </script>
@endif