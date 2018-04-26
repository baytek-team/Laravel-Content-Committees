<div class="field{{ $errors->has('title') ? ' error' : '' }}">
    <label for="title">{{ ___('Title') }}</label>
    <input type="text" id="title" name="title" placeholder="{{ ___('Title') }}" value="{{ old('title', $file->title) }}">
</div>

<div class="field{{ $errors->has('isNotRestricted') ? ' error' : '' }}">
    <div class="ui toggle checkbox">
      <input type="checkbox" id="isNotRestricted" name="isNotRestricted" value="1" @if(old('isNotRestricted', $isNotRestricted)) checked="checked" @endif >
      <label for="isNotRestricted">{{ ___('Viewable by all ROGC members') }}</label>
    </div>
</div>
