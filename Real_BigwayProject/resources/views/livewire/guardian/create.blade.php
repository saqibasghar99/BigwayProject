<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('guardian.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.guardian.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="guardian.name">
        <div class="validation-message">
            {{ $errors->first('guardian.name') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('user.email') ? 'invalid' : '' }}">
        <label class="form-label" for="email">{{ trans('cruds.user.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" required wire:model.defer="user.email">
        <div class="validation-message">
            {{ $errors->first('user.email') }}
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
    <div class="form-group {{ $errors->has('guardian.address') ? 'invalid' : '' }}">
        <label class="form-label" for="address">{{ trans('cruds.guardian.fields.address') }}</label>
        <input class="form-control" type="text" name="address" id="address" wire:model.defer="guardian.address">
        <div class="validation-message">
            {{ $errors->first('guardian.address') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('mediaCollections.guardian_profile_picture') ? 'invalid' : '' }}">
        <label class="form-label" for="profile_picture">{{ trans('cruds.guardian.fields.profile_picture') }}</label>
        <x-dropzone id="profile_picture" name="profile_picture" action="{{ route('admin.guardians.storeMedia') }}" collection-name="guardian_profile_picture" max-file-size="2" max-width="4096" max-height="4096" max-files="1" />
        <div class="validation-message">
            {{ $errors->first('mediaCollections.guardian_profile_picture') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('guardian.gender') ? 'invalid' : '' }}">
        <label class="form-label">{{ trans('cruds.guardian.fields.gender') }}</label>
        @foreach($this->listsForFields['gender'] as $key => $value)
            <label class="radio-label"><input type="radio" name="gender" wire:model="guardian.gender" value="{{ $key }}">{{ $value }}</label>
        @endforeach
        <div class="validation-message">
            {{ $errors->first('guardian.gender') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
