<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Branch;
use DB;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Auth;

class ViewProduct extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    public $isProductOpen = false;
    public $units;
    public $brands;
    public $suppliers;
    public $generic_image_url;
    public $productId;
    public $name;
    public $unit_type;
    public $purchase_price;
    public $units_per_package;
    public $base_price;
    public $tax_id;
    public $final_price;
    public $description;
    public $image_path;
    public $barcode;
    public $sunatcode;
    public $brand_id;
    public $supplier_id;
    public $product_child;
    public $productCategories = [];
    public $presentations = [];
    public $isCategoryOpen = false;
    public $categoriesFull;
    public $branch_id;
    public $temporaryCategories;
    public $categories;
    public $branches;
    public $branchArray = [];
    protected $listeners = ['viewProduct' => 'see','deleteProduct'];
    protected function rules()
    {
        $rules = [
            'name' => 'required|string',
            'unit_type' => 'required',
            'units_per_package' => 'required|integer'
        ];

        if ($this->barcode) {
            $rules['barcode'] = ['unique:products,barcode,' . $this->productId];
        }
        if ($this->generic_image_url) {
            $rules['generic_image_url'] = 'image';
        }
        return $rules;
    }

    protected $messages = [
        'generic_image_url.image' => 'El archivo debe ser una imagen.',
        'generic_image_url.max' => 'La imagen no debe ser mayor a 10MB.',
        'name.required' => 'El nombre del producto es obligatorio.',
        'name.string' => 'El nombre del producto debe ser una cadena de texto.',
        'unit_type.required' => 'Debe seleccionar un tipo de unidad.',
        'barcode.unique' => 'Ya existe un producto con el mismo código de barras',
        'units_per_package.required' => 'Debe haber al menos 1 unidad por envase',
        'units_per_package.integer' => 'La cantidad debe ser un valor entero',
    ];
    public function mount()
    {
        $this->branches = Branch::all();
        $this->tax_id = 1;
        $this->units = Unit::all();
        $this->brands = Brand::all();
        $this->suppliers = Supplier::all();
        $this->unit_type = $this->units->first()->id ?? null;
        $this->categories = Category::with('children')->whereNull('parent_id')->get();
        $this->categoriesFull = Category::get()
            ->keyBy('id');
    }
    public function render()
    {
        return view('livewire.view-product');
    }
    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $coverImagePath = null;
            if ($this->generic_image_url) {
                $coverImagePath = $this->storeCoverImage($this->generic_image_url);
            }

            $productData = [
                'name' => $this->name,
                'unit_type' => $this->unit_type,
                'purchase_price' => $this->purchase_price,
                'units_per_package' => $this->units_per_package,
                'base_price' => $this->base_price,
                'igv' => $this->tax_id,
                'generic_image_url' => $coverImagePath,
                'final_price' => $this->final_price,
                'description' => $this->description,
                'barcode' => $this->barcode,
                'sunatcode' => $this->sunatcode,
                'brand_id' => $this->brand_id,
                'supplier_id' => $this->supplier_id,
                'branch_id' => $this->branch_id
            ];


            if ($this->productId) {

                $product = Product::findOrFail($this->productId);

                if ($coverImagePath != null && $coverImagePath != $product->generic_image_url) {

                    Storage::delete('public/' . $product->generic_image_url);
                }
                if ($coverImagePath == null && $this->image_path == null && $product->generic_image_url != null) {

                    Storage::delete('public/' . $product->generic_image_url);
                }
                if ($coverImagePath == null) {
                    $productData['generic_image_url'] = $this->image_path;
                }
                $productData['updated_by'] = Auth::id();
                $product->update($productData);
                //$this->updateChildProducts($product);

                $this->alert('success', 'Producto actualizado con éxito.');
            } else {
                $productData['code'] = Str::random(15);
                $productData['created_by'] = Auth::id();
                $product = Product::create($productData);
                $this->alert('success', 'Producto registrado con éxito.');
            }

            if ($this->productCategories) {
                $product->categories()->sync($this->productCategories);
            }

            // Guardar las presentaciones del producto
            $this->savePresentations($product);

            // Guardar el stock de productos por sucursal
            $this->saveBranchStocks($product);

            DB::commit();

            $this->dispatch('ProductSaved');
            //$this->searching();
            $this->closeForm();

        } catch (QueryException $e) {
            DB::rollBack();
            $errorCode = $e->errorInfo[1];
            $this->alert('error', 'Hubo un problema al actualizar el producto (' . $errorCode . ') ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Hubo un problema al actualizar el producto: ' . $e->getMessage());
        }
    }
    protected function savePresentations($product)
    {
        // Eliminar presentaciones existentes si es una actualización
        $product->presentations()->delete();

        // Guardar nuevas presentaciones
        foreach ($this->presentations as $presentation) {
            $product->presentations()->create([
                'barcode' => $presentation['barcode'],
                'unit' => $presentation['unit'],
                'description' => $presentation['description'],
                'factor' => $presentation['factor'],
                'price' => $presentation['price'],
            ]);
        }
    }
    protected function saveBranchStocks($product)
    {
        foreach ($this->branchArray as $branchId => $stock) {
            ProductStock::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'branch_id' => $branchId,
                ],
                [
                    'stock' => $stock
                ]
            );
        }
    }
    public function see($productCode)
    {

        $product = Product::where('code', $productCode)->first();

        if (!$product) {
            return $this->alert('error', 'Producto No encontrado');
        }

        $this->openForm();

        $this->productId = $product->id;
        $this->name = $product->name;
        $this->unit_type = $product->unit_type;
        $this->purchase_price = $product->purchase_price;
        $this->units_per_package = $product->units_per_package;
        $this->base_price = $product->base_price;
        $this->tax_id = $product->igv;
        $this->final_price = $product->final_price;
        $this->description = $product->description;
        $this->image_path = $product->generic_image_url;
        $this->barcode = $product->barcode;
        $this->sunatcode = $product->sunatcode;
        $this->brand_id = $product->brand_id;
        $this->supplier_id = $product->supplier_id;
        $this->product_child = $product->child;
        $this->productCategories = $product->categories->pluck("id");
        $this->presentations = $product->presentations()->get()->toArray();
        $this->branchArray = $product->stocks()->pluck('stock','branch_id')->toArray();

    }
    public function addPresentation()
    {
        if (!$this->productId) {
            return;
        }
        $this->presentations[] = [
            'barcode' => '',
            'unit' => 1,
            'description' => '',
            'factor' => 1,
            'price' => '0',
        ];
    }
    public function deletePresentation($index)
    {
        unset($this->presentations[$index]);
    }
    public function openForm()
    {
        $this->resetForm();
        $this->isProductOpen = true;
    }
    public function closeForm()
    {
        $this->resetForm();
        $this->isProductOpen = false;
    }
    public function resetForm()
    {
        $this->productId = null;
        $this->name = null;
        $this->purchase_price = null;
        $this->units_per_package = null;
        $this->base_price = null;
        $this->tax_id = 1;
        $this->final_price = null;
        $this->description = null;
        $this->image_path = null;
        $this->generic_image_url = null;
        $this->barcode = null;
        $this->sunatcode = null;
        $this->brand_id = Brand::first()->id ?? null;
        $this->supplier_id = Supplier::first()->id ?? null;
    }
    public function deleteImage()
    {
        $this->generic_image_url = null;
        $this->image_path = null;
    }
    public function calculateFinalPrice()
    {
        try {
            if ($this->base_price) {
                $taxRate = $this->getTaxRate(); // Obtén la tasa de impuesto correspondiente
                $this->final_price = round($this->base_price * (1 + $taxRate), 2);
            }
        } catch (\Exception $e) {
            $this->final_price = 0; // Setea un número válido en caso de error
        }
    }

    public function calculateBasePrice()
    {
        try {
            if ($this->final_price) {
                $taxRate = $this->getTaxRate(); // Obtén la tasa de impuesto correspondiente
                $this->base_price = round($this->final_price / (1 + $taxRate), 2);
            }
        } catch (\Exception $e) {
            $this->base_price = 0; // Setea un número válido en caso de error
        }
    }

    private function getTaxRate()
    {
        // Devuelve la tasa de impuesto basada en el ID de impuesto
        // Por ejemplo, si tax_id es 1, devuelve 0.18 (18%)
        return $this->tax_id == 1 ? 0.18 : 0; // Ajusta esta lógica según sea necesario
    }

    public function removeCategory($categoryId)
    {
        $productCategoriesArray = $this->productCategories->toArray();

        // Buscar el índice del categoryId en el array
        $index = array_search($categoryId, $productCategoriesArray);

        if ($index !== false) {
            // Eliminar el elemento del array
            unset($productCategoriesArray[$index]);

            // Reindexar el array para evitar huecos
            $productCategoriesArray = array_values($productCategoriesArray);

            // Convertir el array de nuevo a colección y actualizar la propiedad
            $this->productCategories = collect($productCategoriesArray);
        }
    }
    public function openCategories()
    {
        $this->isCategoryOpen = true;
    }
    public function askDeleteProduct(){
        if(!$this->productId){
            $this->alert('error','El producto No esta Seleccionado');
        }

        $this->alert('question', '¿Seguro que desea eliminar este Producto?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Si',
            'cancelButtonText' => 'No, Cancelar',
            'onConfirmed' => 'deleteProduct',
            'onDismissed' => 'cancelled',
            'position' => 'center',
            'toast' => false,
            'timer' => null,
            'confirmButtonColor' => '#F5922A',
            'cancelButtonColor' => '#2C2C2C',
        ]);
    }
    public function deleteProduct(){
        if(!$this->productId){
            $this->alert('error','El producto No esta Seleccionado');
        }

        $producto = Product::find($this->productId);
        if($producto){
            $producto->is_active = false;
            $producto->save();
        }
        $this->dispatch('ProductSaved');
        $this->closeForm();
    }
}
