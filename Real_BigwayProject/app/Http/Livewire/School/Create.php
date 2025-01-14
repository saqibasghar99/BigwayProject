<?php

namespace App\Http\Livewire\School;

use App\Models\Location;
use App\Models\School;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public School $school;

    public array $user = [
        'name' => '',
        'email' => '',
        'password' => '',
        'contact' => '',
        'type' => '',
    ];

    public array $listsForFields = [];

    public function mount(School $school)
    {
        $this->school = $school;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.school.create');
    }

    public function submit()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->school['name'],
            'email' => $this->user['email'],
            'password' => $this->user['password'],
            'contact' => $this->user['contact'],
            'type' => 'school',
        ]);

        
        $this->school->name = $user->name;
        $this->school->user_id = $user->id;

        $this->school->save();
        
        return redirect()->route('admin.schools.index');
    }

    protected function rules(): array
    {
        return [
            'school.name' => [
                'string',
                'required',
            ],
            'user.email' => [
                'email:rfc',
                'required',
                'unique:users,email',
            ],
            'user.password' => [
                'string',
                'required',
            ],
            'school.address' => [
                'string',
                'required',
            ],
            'user.contact' => [
                'string',
                'nullable',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['location'] = Location::pluck('latitude', 'id')->toArray();
        $this->listsForFields['user']     = User::pluck('name', 'id')->toArray();
    }
}
