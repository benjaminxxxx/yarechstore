<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use DB;

class AdminBranch extends Component
{
    public $branches;
    public $companies;
    public $branchId;
    public $company_id;
    public $name;
    public $address;
    public $code;
    public $isFormOpen;

    public function mount()
    {
        $this->companies = Company::all();
        $this->company_id = $this->companies->first()->id ?? null; // Protección contra vacío
    }

    public function render()
    {
        $this->branches = Branch::all();
        return view('livewire.admin-branch');
    }

    public function store()
{
    $this->validate([
        'company_id' => 'required|exists:companies,id',
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255'
    ]);

    $data = [
        'company_id' => $this->company_id,
        'name' => $this->name,
        'address' => $this->address,
    ];

    DB::beginTransaction();

    try {
        if ($this->branchId) {
            $branch = Branch::findOrFail($this->branchId);
            $branch->update($data);
            session()->flash('message', __('Branch successfully updated.'));
        } else {
            $data['code'] = Str::random(15);
            Branch::create($data);
            session()->flash('message', __('Branch successfully created.'));
        }

        DB::commit();
        $this->closeForm();
    } catch (QueryException $e) {
        DB::rollBack();
        session()->flash('error', __('There was an error storing data: ') . $e->getMessage());
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
    }
}


    public function delete($code)
    {
        try {
            $branch = Branch::where('code', $code)->firstOrFail();
            $branch->delete();

            session()->flash('message', __('Branch successfully deleted.'));
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Branch not found.'));
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred.'));
        }
    }

    public function resetFields()
    {
        $this->branchId = null;
        $this->name = '';
        $this->address = '';
        $this->code = '';
    }

    public function edit($code)
    {
        try {
            $branch = Branch::where('code', $code)->firstOrFail();
            $this->branchId = $branch->id;
            $this->company_id = $branch->company_id;
            $this->name = $branch->name;
            $this->address = $branch->address;
            $this->openForm();
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Branch not found: ') . $e->getMessage());
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
