<div class="form-group row">
    <label for="{{ $id }}_1" class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-10">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="{{ $id }}" id="{{ $id }}_0">
            <label class="form-check-label block" for="{{ $id }}_0">
                {{ $label_0 ?? 'no' }}
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="radio" name="{{ $id }}" id="{{ $id }}_1">
            <label class="form-check-label" for="{{ $id }}_1">
                {{ $label_1 ?? 'yes' }}
            </label>
        </div>
        @if ($errors->has($id))
            <span class="text-danger">{{ $errors->first($id) }}</span>
        @endif
    </div>
</div>




