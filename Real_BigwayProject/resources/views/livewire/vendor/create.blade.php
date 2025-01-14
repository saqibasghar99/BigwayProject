<form wire:submit.prevent="submit" class="pt-3">
    <div class="form-group {{ $errors->has('vendor.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.vendor.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="vendor.name">
        <div class="validation-message">
            {{ $errors->first('vendor.name') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.contact') ? 'invalid' : '' }}">
        <label class="form-label required" for="contact">{{ trans('cruds.user.fields.contact') }}</label>
        <input class="form-control" type="text" name="contact" id="contact" required wire:model.defer="user.contact">
        <div class="validation-message">
            {{ $errors->first('user.contact') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.password') ? 'invalid' : '' }}">
        <label class="form-label required" for="password">{{ trans('cruds.user.fields.password') }}</label>
        <input class="form-control" type="password" name="password" id="password" required wire:model.defer="user.password">
        <div class="validation-message">
            {{ $errors->first('user.password') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.email') ? 'invalid' : '' }}">
        <label class="form-label" for="email">{{ trans('cruds.user.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" wire:model.defer="user.email">
        <div class="validation-message">
            {{ $errors->first('user.email') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('mediaCollections.vendor_profile_picture') ? 'invalid' : '' }}">
        <label class="form-label" for="profile_picture">{{ trans('cruds.vendor.fields.profile_picture') }}</label>
        <x-dropzone id="profile_picture" name="profile_picture" action="{{ route('admin.vendors.storeMedia') }}" collection-name="vendor_profile_picture" max-file-size="2" max-width="4096" max-height="4096" max-files="1" />
        <div class="validation-message">
            {{ $errors->first('mediaCollections.vendor_profile_picture') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('vendor.gender') ? 'invalid' : '' }}">
        <label class="form-label">{{ trans('cruds.vendor.fields.gender') }}</label>
        @foreach($this->listsForFields['gender'] as $key => $value)
            <label class="radio-label"><input type="radio" name="gender" wire:model="vendor.gender" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('vendor.gender') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
