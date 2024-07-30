<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use DB;

class AdminInventory extends Component
{
    public $products;
    public $inventoryId;
    public $product_id;
    public $stock;
    public $minimum_stock;
    public $location;
    public $expiry_date;
    public $perPage=10;
    public $branchCode;
    public $branches;
    public $product_name;

    public function mount()
    {
        $this->branchCode = Session::get('selected_branch');
        $this->branches = Branch::all();
        $this->products = Product::all();
        $this->product_id = $this->products->first()->id ?? null; // Protección contra vacío
    }

    public function render()
    {
        $inventoryItems = collect();

        if ($this->branchCode) {
            $branch = Branch::where('code', $this->branchCode)->first();
            
            if ($branch) {
                $inventoryItems = $branch->products()->with('inventories')->paginate($this->perPage);
                
              
            }
        }
        
        return view('livewire.admin-inventory',[
            'inventoryItems'=>$inventoryItems
        ]);
    }

    public function store()
    {
        $this->resetErrorBag();

        $this->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date'
        ]);

        $data = [
            'product_id' => $this->product_id,
            'stock' => $this->stock,
            'minimum_stock' => $this->minimum_stock,
            'location' => $this->location,
            'expiry_date' => $this->expiry_date,
        ];


        try {
            if ($this->inventoryId) {
                $inventory = Inventory::findOrFail($this->inventoryId);
                $inventory->update($data);
                session()->flash('message', __('Inventory successfully updated.'));
            }

            $this->closeForm();
        } catch (QueryException $e) {
            session()->flash('error', __('There was an error storing data: ') . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
        }
    }
    public function see($productCode){
        $product = Product::where('code', $productCode)->first();

        // Verificar si el producto existe
        if (!$product) {
            session()->flash('error', __('Product not found.'));
            return;
        }

        // Verificar si el producto está en el inventario de la sucursal actual
        $branch = Branch::where('code', $this->branchCode)->first();

        if (!$branch) {
            session()->flash('error', __('Branch not found.'));
            return;
        }

        // Buscar el inventario del producto en la sucursal actual
        $inventory = Inventory::where('branch_id', $branch->id)
        ->where('product_id', $product->id)
        ->first();

        // Si el inventario no existe, crear uno con valores por defecto
        if (!$inventory) {
            $inventory = Inventory::create([
                'branch_id' => $branch->id,
                'product_id' => $product->id,
                'stock' => 0,
                'minimum_stock' => 0,
                'location' => null,
                'expiry_date' => null,
            ]);
        }
        $this->product_name = $product->name;
        $this->inventoryId = $inventory->id;
        $this->product_id = $inventory->product_id;
        $this->stock = $inventory->stock;
        $this->minimum_stock = $inventory->minimum_stock;
        $this->location = $inventory->location;
        $this->expiry_date = $inventory->expiry_date;
    }
    public function delete($id)
    {
        try {
            $inventory = Inventory::findOrFail($id);
            $inventory->delete();

            session()->flash('message', __('Inventory successfully deleted.'));
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Inventory not found.'));
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred.'));
        }
    }

    public function resetFields()
    {
        $this->resetErrorBag();
        $this->inventoryId = null;
        $this->product_id = null;
        $this->product_name = null;
        $this->stock = null;
        $this->minimum_stock = null;
        $this->location = null;
        $this->expiry_date = null;
    }

    public function edit($id)
    {
        try {
            $inventory = Inventory::findOrFail($id);
            $this->inventoryId = $inventory->id;
            $this->product_id = $inventory->product_id;
            $this->stock = $inventory->stock;
            $this->minimum_stock = $inventory->minimum_stock;
            $this->location = $inventory->location;
            $this->expiry_date = $inventory->expiry_date;
        } catch (ModelNotFoundException $e) {
            session()->flash('error', __('Inventory not found: ') . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
        }
    }

    public function closeForm()
    {
        $this->resetFields();
    }
}
