<div class="form-group row">
    <label for="{{ $id }}" class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-10">
        <select class="form-control @if ($errors->has($id)) is-invalid @endif" id="{{ $id }}" name="{{ $id }}">
            {{ $slot }}
        </select>
        @if ($errors->has($id))
            <span class="text-danger">{{ $errors->first($id) }}</span>
        @endif
    </div>
</div>
