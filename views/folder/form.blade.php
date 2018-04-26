@if($parents)

<div class="field">
	<label for="parent_id">Parent</label>
	<select name="parent_id" id="parent_id" class="ui search dropdown">
		<option value="">{{$committee->title}}</option>
		@foreach($parents as $item)
			@php
				//Reenable selection of items after its been disabled
				if ($disabledFlag && $item->depth <= $disabledDepth) {
					$disabledFlag = false;
				}

				//Prevent selection of the current folder or its children
				if ($folder->id == $item->id) {
					$disabledFlag = true;
					$disabledDepth = $item->depth;
				}
			@endphp

			<option value="{{ $item->id }}"
				@if(isset($parent) && $parent->id == $item->id) selected="selected"@endif 
				@if($disabledFlag) disabled @endif>{!! str_repeat('- ', $item->depth) !!}{{ $item->title }}</option>
		@endforeach
	</select>
</div>

@else
	@if($folder->id)
		@section('page.head.menu')
		    <div class="ui secondary menu">
	            <a class="item" href="{{route('committees.folder.edit.parent', [$committee, $folder])}}">
	                <i class="arrow circle outline right icon"></i>{{ ___('Move Folder') }}
	            </a>
		    </div>
		@endsection
	@endif

	<input type="hidden" id="parent_id" name="parent_id" value="{{ $parent->id }}">
	<div class="field">
		<label>Parent</label>
		<input type="text" disabled value="{{$parent->title}}">
	</div>
@endif

<div class="two fields">
	<div class="twelve wide field{{ $errors->has('title') ? ' error' : '' }}">
		<label for="title">{{ ___('Title') }}</label>
		<input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $folder->title) }}">
	</div>

	<div class="four wide field{{ $errors->has('order') ? ' error' : '' }}">
		<label for="order">{{ ___('Order') }}</label>
		<input type="text" id="order" name="order" placeholder="#" value="{{ old('order', $folder->order) }}">
	</div>
</div>