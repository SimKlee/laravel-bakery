<div class="form-group row">
    <label for="{{ $id }}" class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-10">
        <input type="text" class="form-control no-focus @if ($errors->has($id)) is-invalid @endif" id="{{ $id }}" name="{{ $id }}" foreign-id="{{ $foreignId }}"
               readonly tabindex="9999">
        @if ($errors->has($id))
            <span class="text-danger">{{ $errors->first($id) }}</span>
        @endif
    </div>
</div>
<script type="text/javascript">
    // @TODO: make a jquery plugin Slug; referenced by foreign-id
    $(function () {
        $('#{{ $foreignId }}').change(function () {
            $.get('/slug/' + unescape($('#{{ $foreignId }}').val()), function (data) {
                $('#{{ $id }}').val(data);
            });
        });
        $('#{{ $foreignId }}').keyup(function () {
            $.get('/slug/' + unescape($('#{{ $foreignId }}').val()), function (data) {
                $('#{{ $id }}').val(data);
            });
        });
    });
</script>
