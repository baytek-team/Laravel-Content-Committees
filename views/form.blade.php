<div class="field{{ $errors->has('title') ? ' error' : '' }}">
    <label for="title">{{ ___('Title') }}</label>
    <input type="text" id="title" name="title" placeholder="{{ ___('Title') }}" value="{{ old('title', $committee->title) }}">
</div>
