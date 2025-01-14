<?php

namespace App\Http\Livewire\Guardian;

use App\Models\Guardian;
use App\Models\User;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Create extends Component
{
    public Guardian $guardian;

    public array $mediaToRemove = [];

    public array $user = [
        "email" => "",
        "contact" => "",
        "password" => "",
    ];

    public array $listsForFields = [ ];

    public array $mediaCollections = [];

    public function addMedia($media): void
    {
        $this->mediaCollections[$media['collection_name']][] = $media;
    }

    public function removeMedia($media): void
    {
        $collection = collect($this->mediaCollections[$media['collection_name']]);

        $this->mediaCollections[$media['collection_name']] = $collection->reject(fn ($item) => $item['uuid'] === $media['uuid'])->toArray();

        $this->mediaToRemove[] = $media['uuid'];
    }

    protected function syncMedia(): void
    {
        collect($this->mediaCollections)->flatten(1)
            ->each(fn ($item) => Media::where('uuid', $item['uuid'])
                ->update(['model_id' => $this->guardian->id]));

        Media::whereIn('uuid', $this->mediaToRemove)->delete();
    }

    public function mount(Guardian $guardian)
    {
        $this->guardian = $guardian;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.guardian.create');
    }

    public function submit()
    {
        $this->validate();
        $result = User::create([
            'name'=> $this->guardian['name'],
            'email'=> $this->user['email'],
            'contact' => $this->user['contact'],
            'password' => bcrypt($this->user['password']),
            'type' => 'guardian',
            'status' => 'active'
        ]);
        $this->guardian->status = 'active';
        $this->guardian->user_id = $result->id;
        $this->guardian->save();
        $this->syncMedia();

        return redirect()->route('admin.guardians.index');
    }

    protected function rules(): array
    {
        return [
            'guardian.name' => [
                'string',
                'required',
            ],
            'guardian.address' => [
                'string',
                'nullable',
            ],
            'mediaCollections.guardian_profile_picture' => [
                'array',
                'nullable',
            ],
            'mediaCollections.guardian_profile_picture.*.id' => [
                'integer',
                'exists:media,id',
            ],
            'guardian.gender' => [
                'nullable',
                'in:' . implode(',', array_keys($this->listsForFields['gender'])),
            ],
            'user.contact' => [
                'string',
                'required',
            ],
            'user.email' => [
                'email:rfc',
                'nullable',
                'unique:users,email',
            ],
            'user.password' => [
                'string',
                'required',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['gender'] = $this->guardian::GENDER_RADIO;
    }
}
