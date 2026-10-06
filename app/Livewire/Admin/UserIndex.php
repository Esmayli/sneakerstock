<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $users = User::where('name', 'like', '%'.$this->search.'%')
            ->orwhere('email', 'like', '%'.$this->search.'%')
            ->paginate();

        return view('livewire.admin.user-index', compact('users'));
    }
}
