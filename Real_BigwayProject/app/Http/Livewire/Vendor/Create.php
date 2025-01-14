<?php

namespace App\Http\Livewire\Vendor;

use App\Models\User;
use App\Models\Vendor;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Create extends Component
{
    public Vendor $vendor;

    public array $user = [
        "email" => "",
        "contact" => "",
        "password" => "",
    ];
    public array $mediaToRemove = [];
    public array $listsForFields = [];
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
                ->update(['model_id' => $this->vendor->id]));

        Media::whereIn('uuid', $this->mediaToRemove)->delete();
    }

    public function mount(Vendor $vendor)
    {
        $this->vendor = $vendor;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.vendor.create');
    }

    public function submit()
    {
        $this->validate();
        $result = User::create([
            'name'=> $this->vendor['name'],
            'email'=> $this->user['email'],
            'contact' => $this->user['contact'],
            'password' => bcrypt($this->user['password']),
            'type' => 'vendor',
            'status' => 'active'
        ]);
        $this->vendor->status = 'active';
        $this->vendor->user_id = $result->id;
        $this->vendor->save();
        $this->syncMedia();

        return redirect()->route('admin.vendors.index');
    }

    protected function rules(): array
    {
        return [
            'vendor.name' => [
                'string',
                'required',
            ],
            'mediaCollections.vendor_profile_picture' => [
                'array',
                'nullable',
            ],
            'mediaCollections.vendor_profile_picture.*.id' => [
                'integer',
                'exists:media,id',
            ],
            'vendor.gender' => [
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
        $this->listsForFields['user']   = User::pluck('name', 'id')->toArray();
        $this->listsForFields['gender'] = $this->vendor::GENDER_RADIO;
    }
}
