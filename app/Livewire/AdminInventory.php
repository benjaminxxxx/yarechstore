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
    public $inventoryId;
    public $product_id;
    public $stock;
    public $minimum_stock;
    public $location;
    public $expiry_date;
    public $perPage = 10;
    public $branchCode;
    public $branches;
    public $product_name;
    public $search;

    public function mount()
    {
        $this->branchCode = Session::get('selected_branch');
        $this->branches = Branch::all();
    }

    public function render()
    {
        $inventoryItems = collect();

        if ($this->branchCode) {
            $branch = Branch::where('code', $this->branchCode)->first();
            

            if ($branch)
                $inventoryItems = $branch->products()->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                })->with('inventories')->paginate($this->perPage);
               
        }

        return view('livewire.admin-inventory', [
            'inventoryItems' => $inventoryItems
        ]);
    }

    public function store()
    {

        $this->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer',
            'minimum_stock' => 'required|integer'
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

            $this->resetFields();
        } catch (QueryException $e) {
            session()->flash('error', __('There was an error storing data: ') . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', __('An unexpected error occurred: ') . $e->getMessage());
        }
    }
    public function see($productCode)
    {
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
        $this->resetFields();
        $this->product_name = $product->name;
        $this->inventoryId = $inventory->id;
        $this->product_id = $inventory->product_id;
        $this->stock = $inventory->stock;
        $this->minimum_stock = $inventory->minimum_stock;
        $this->location = $inventory->location;
        $this->expiry_date = $inventory->expiry_date;
    }
    public function openBox($productCode)
    {
        try {
            // Obtener el producto padre
            $parentProduct = Product::where('code', $productCode)->firstOrFail();

            // Verificar si el producto padre tiene hijos
            $childProducts = $parentProduct->child()->get();
            if ($childProducts->isEmpty()) {
                session()->flash('error', __('No child products found.'));
                return;
            }

            // Calcular la cantidad de stock que se debe agregar
            $stockToAdd = $parentProduct->units_per_package;

            // Obtener la sucursal actual
            $branch = Branch::where('code', $this->branchCode)->firstOrFail();
            $parentInventory = Inventory::where('branch_id', $branch->id)
                ->where('product_id', $parentProduct->id)
                ->first();

            if (!$parentInventory) {
                session()->flash('error', __('Parent product is not in inventory.'));
                return;
            }

            if ($parentInventory->stock <= 0) {
                session()->flash('error', __('Not enough stock in parent product.'));
                return;
            }

            // Disminuir el stock del producto padre
            $parentInventory->stock -= 1;
            $parentInventory->save();

            foreach ($childProducts as $childProduct) {
                // Verificar si el producto hijo está en el inventario de la sucursal
                $inventory = Inventory::where('branch_id', $branch->id)
                    ->where('product_id', $childProduct->id)
                    ->first();

                if (!$inventory) {
                    // Si no existe, crear una nueva entrada en el inventario
                    Inventory::create([
                        'branch_id' => $branch->id,
                        'product_id' => $childProduct->id,
                        'stock' => $stockToAdd,
                        'minimum_stock' => 0,
                        'location' => null,
                        'expiry_date' => null,
                    ]);
                } else {
                    // Si existe, actualizar el stock
                    $inventory->stock += $stockToAdd;
                    $inventory->save();
                }
            }

            session()->flash('message', __('Stock updated successfully.'));
            $this->closeForm(); // Cerrar el formulario si es necesario

        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un problema al actualizar el stock: ' . $e->getMessage());
        }
    }
    public function package($productCode)
    {
        try {
            // Obtener el producto hijo
            $childProduct = Product::where('code', $productCode)->firstOrFail();

            // Obtener el producto padre
            $parentProduct = $childProduct->parent()->firstOrFail();

            // Verificar si el producto padre tiene un envase definido
            if ($parentProduct->units_per_package <= 0) {
                session()->flash('error', __('Parent product does not have a valid units_per_package.'));
                return;
            }

            // Obtener la sucursal actual
            $branch = Branch::where('code', $this->branchCode)->firstOrFail();

            // Verificar el stock del producto hijo
            $childInventory = Inventory::where('branch_id', $branch->id)
                ->where('product_id', $childProduct->id)
                ->first();

            if (!$childInventory || $childInventory->stock < $parentProduct->units_per_package) {
                session()->flash('error', __('Not enough stock in child product to create a package.'));
                return;
            }

            // Disminuir el stock del producto hijo
            $childInventory->stock -= $parentProduct->units_per_package;
            $childInventory->save();

            // Actualizar o crear el inventario del producto padre
            $parentInventory = Inventory::where('branch_id', $branch->id)
                ->where('product_id', $parentProduct->id)
                ->first();

            if (!$parentInventory) {
                // Si no existe, crear una nueva entrada en el inventario
                Inventory::create([
                    'branch_id' => $branch->id,
                    'product_id' => $parentProduct->id,
                    'stock' => 1,
                    'minimum_stock' => 0,
                    'location' => null,
                    'expiry_date' => null,
                ]);
            } else {
                // Si existe, actualizar el stock
                $parentInventory->stock += 1;
                $parentInventory->save();
            }

            session()->flash('message', __('Stock updated successfully.'));
            $this->closeForm(); // Cerrar el formulario si es necesario

        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un problema al empaquetar el stock: ' . $e->getMessage());
        }
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
        $this->reset([
            'inventoryId',
            'product_id',
            'product_name',
            'stock',
            'minimum_stock',
            'location',
            'expiry_date'
        ]);
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
