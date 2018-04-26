<div class="field">
@if($member->id)
    <label for="member">{{ ___('Member') }}</label>
    <input type="hidden" name="member" value="{{ old('member', isset($member) ? $member->id:'')}}">
    <input type="text" value="{{$member->name}}" disabled="disabled">
@else
    <label for="member">{{ ___('Members') }}</label>
    <div class="ui fluid dropdown labeled search icon basic button">
        <input type="hidden" name="member" value="{{ old('member', isset($member) ? $member->id:'')}}">
        <i class="search icon"></i>
        <span class="text">{{ ___('Click to choose a member') }}</span>
        <div class="menu transition hidden">
            @foreach($members as $member)
                <div class="item" data-value="{{ $member->id }}">{{ $member->name }}</div>
            @endforeach
        </div>
    </div>
@endif
</div>

<div class="two fields">
    <div class="twelve wide field{{ $errors->has('title') ? ' error' : '' }}">
        <label for="title">{{ ___("Member's Committee Title") }}</label>
        <input type="text" id="title" name="title"  placeholder="{{ ___('Member title') }}" value="{{ $pivot->title }}">
    </div>

    <div class="four wide field{{ $errors->has('sorting') ? ' error' : '' }}">
        <label for="sorting">{{ ___("Order") }}</label>
        <input type="text" id="sorting" name="sorting"  placeholder="{{ ___('Order') }}" value="{{ old('sorting', isset($pivot) && isset($pivot->sorting) ? $pivot->sorting : 0 ) }}">
    </div>
</div>

<div class="inline fields">
    <div class="field">
        <div class="ui checkbox">
          <input name="admin" type="checkbox" @if($pivot->admin) checked @endif>
          <label>Member can upload documents</label>
        </div>
    </div>
    <div class="field">
        <div class="ui checkbox">
          <input name="notify" type="checkbox" @if($pivot->notifications) checked @endif>
          <label>Member receives feedback emails</label>
        </div>
    </div>
</div>