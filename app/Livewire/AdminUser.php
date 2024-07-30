<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\UserBranch;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminUser extends Component
{
    use WithPagination;

    public $roles;
    public $userId;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $isFormOpen;
    public $search = '';
    public $perPage = 10;
    public $assignedBranch = [];
    public $branches;

    public function mount()
    {
        $this->roles = Role::all();
        $this->branches = Branch::all();
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);

        return view('livewire.admin-user', [
            'users' => $users,
            'roles' => Role::all(), // Fetch roles for the form
        ]);
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'assignedBranch' => 'required|array',
            'assignedBranch.*' => 'exists:branches,id',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        try {

            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->update($data);
                session()->flash('message', __('User successfully updated.'));
            } else {
                $data['code'] = Str::random(15);
                $user = User::create($data);
                session()->flash('message', __('User successfully created.'));
            }

            UserBranch::where('user_id', $user->id)->delete();
            foreach ($this->assignedBranch as $branchId) {
                UserBranch::create([
                    'user_id' => $user->id,
                    'branch_id' => $branchId,
                ]);
            }
            $this->closeForm();
        } catch (QueryException $e) {
            session()->flash('error', __('There was an error storing data: ') . $e->getMessage());
        }
    }

    public function delete($code)
    {
        try {
            $user = User::where('code', $code)->firstOrFail();
            $user->delete();

            session()->flash('message', __('User successfully deleted.'));
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('User not found.'));
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred.'));
        }
    }

    public function resetFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->assignedBranch = [];
        $this->password = '';
        $this->role_id = '';
    }

    public function edit($code)
    {
        try {
            $user = User::where('code', $code)->firstOrFail();
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->assignedBranch = $user->branches->pluck('id');
            $this->role_id = $user->role_id;
            $this->openForm();
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('User not found: ') . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
        }
    }

    public function openForm()
    {
        $this->isFormOpen = true;
    }

    public function closeForm()
    {
        $this->resetFields();
        $this->isFormOpen = false;
    }
}
