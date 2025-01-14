<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('school.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.school.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="school.name">
        <div class="validation-message">
            {{ $errors->first('school.name') }}
        </div>
    </div>

    <div class="form-group {{ $errors->has('user.email') ? 'invalid' : '' }}">
        <label class="form-label required" for="email">{{ trans('cruds.user.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" required wire:model.defer="user.email">
        <div class="validation-message">
            {{ $errors->first('user.email') }}
        </div>
    </div>

    <div class="form-group {{ $errors->has('user.password') ? 'invalid' : '' }}">
        <label class="form-label required" for="password">{{ trans('cruds.user.fields.password') }}</label>
        <input class="form-control" type="password" name="password" id="email" required wire:model.defer="user.password">
        <div class="validation-message">
            {{ $errors->first('user.password') }}
        </div>
    </div>

    <div class="form-group {{ $errors->has('user.contact') ? 'invalid' : '' }}">
        <label class="form-label required" for="contact">{{ trans('cruds.user.fields.contact') }}</label>
        <input class="form-control" type="text" name="contact" id="contact" required wire:model.defer="user.contact">
        <div class="validation-message">
            {{ $errors->first('user.contact') }}
        </div>
    </div>

    <div class="form-group {{ $errors->has('school.address') ? 'invalid' : '' }}">
        <label class="form-label required" for="address">{{ trans('cruds.school.fields.address') }}</label>
        <input class="form-control" type="text" name="address" id="address" required wire:model.defer="school.address">
        <div class="validation-message">
            {{ $errors->first('school.address') }}
        </div>
    </div>
    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>