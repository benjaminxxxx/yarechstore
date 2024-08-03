<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class ConfigUnits extends Component
{
    public $units;
    public $unitId;
    public $name;
    public $isFormOpen;

    public function mount()
    {
        // Inicialización de variables si es necesario
    }

    public function render()
    {
        $this->units = Unit::with(['products'])->get();
        return view('livewire.config-units');
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255'
        ]);

        $data = [
            'name' => $this->name,
        ];

        try {
            if ($this->unitId) {
                $unit = Unit::findOrFail($this->unitId);
                $unit->update($data);
                session()->flash('message', __('Unit successfully updated.'));
            } else {
                Unit::create($data);
                session()->flash('message', __('Unit successfully created.'));
            }
            $this->closeForm();
        } catch (QueryException $e) {
            session()->flash('error', __('There was an error storing data: ') . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();

            session()->flash('message', __('Unit successfully deleted.'));
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Unit not found.'));
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred.'));
        }
    }

    public function resetFields()
    {
        $this->unitId = null;
        $this->name = '';
    }

    public function edit($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $this->unitId = $unit->id;
            $this->name = $unit->name;
            $this->openForm();
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Unit not found: ') . $e->getMessage());
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
