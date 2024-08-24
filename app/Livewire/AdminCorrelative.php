<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Correlative;
use App\Models\InvoicesType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use DB;

class AdminCorrelative extends Component
{
    public $branches;
    public $invoiceTypes;
    public $selectedBranch;
    public $selectedInvoiceType;
    public $correlatives;
    public $editingCorrelativeId;
    public $editingCorrelative;
    public $document_type_id;
    public $document_code;
    public $correlative;
    public $serie;
    public $current_correlative;

    public function mount()
    {
        $this->branches = Branch::all();
    }

    public function render()
    {
        $this->loadCorrelatives();
        return view('livewire.admin-correlative');
    }

    public function loadCorrelatives()
    {
        if ($this->selectedBranch) {
            $this->correlatives = Correlative::where('branch_id', $this->selectedBranch)->get();
    
            // Obtener los IDs de los tipos de documentos asociados a los correlativos
            $invoiceTypeIds = $this->correlatives->pluck('invoice_type_id')->toArray();
            // Cargar los tipos de documentos que no están en la lista de correlativos
            $this->invoiceTypes = InvoicesType::whereNotIn('id', $invoiceTypeIds)->get();
        } else {
            $this->correlatives = collect();
            $this->invoiceTypes = InvoicesType::all();
        }
    }

    public function store()
    {
        // Validar los datos del formulario
        $this->validate([
            'selectedInvoiceType' => 'required|exists:invoices_type,id' // Asegúrate de validar el tipo de documento seleccionado
        ]);
    
        try {
            Correlative::create([
                'series' => mb_strtoupper($this->serie),
                'current_correlative' => $this->current_correlative,
                'branch_id' => $this->selectedBranch,
                'invoice_type_id' => $this->selectedInvoiceType,
            ]);

            session()->flash('message', 'Correlativo agregado correctamente');
    
            $this->resetFields(); // Resetear los campos después de la acción
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Correlative not found: ') . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
        }
    }
    

    public function delete($id)
    {
        try {
            $correlative = Correlative::findOrFail($id);
            $correlative->delete();

            session()->flash('message','Correlativo Eliminado Exitosamente');
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Correlative not found.'));
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred.'));
        }
    }

    public function resetFields()
    {
        $this->selectedInvoiceType = null;
        $this->serie = '';
        $this->current_correlative = '';
    }
}
