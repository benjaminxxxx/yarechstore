<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Branch;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Auth;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductsList extends Component
{
    use WithFileUploads;
    public $productId;
    public $products = [];
    public $search = '';
    public $isProductOpen = false;
    public $image_path;
    public $generic_image_url;
    public $units;
    public $tax_id;

    public $name;
    public $unit_type;
    public $purchase_price;
    public $units_per_package;
    public $base_price;
    public $final_price;
    public $description;
    public $barcode;
    public $sunatcode;
    public $productCategories = [];
    public $temporaryCategories;
    public $categories;
    public $supplier_id;
    public $suppliers;
    public $brands;
    public $brand_id;
    public $isCategoryOpen = false;
    public $categoriesFull;
    public $branch_id;
    public $product_child;
    protected function rules()
    {
        $rules = [
            'name' => 'required|string',
            'unit_type' => 'required',
            'units_per_package'=>'required|integer'
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
        $branchCode = Session::get('selected_branch');
        $branch = Branch::where('code', $branchCode)->first();
        $this->branch_id = $branch ? $branch->id : null;

        $this->tax_id = 1;
        $this->products = $branch->products()->whereNull('parent_id')->get();
        $this->units = Unit::all();
        $this->brands = Brand::all();
        $this->suppliers = Supplier::all();
        $this->unit_type = $this->units->first()->id ?? null;
        $this->categories = Category::with('children')->whereNull('parent_id')->get();
        $this->categoriesFull = Category::get()
            ->keyBy('id');
    }

    public function searching()
    {
        $branchCode = Session::get('selected_branch');
        $branch = Branch::where('code', $branchCode)->first();

        $this->products = $branch->products()->whereNull('parent_id')
            ->where('branch_id', $this->branch_id) // Filtra por branch_id
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.products-list');
    }
    public function save()
    {
        $this->validate();

        try {    

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
                $this->updateChildProducts($product);

                session()->flash('message', 'Producto actualizado con éxito.');
            } else {
                $productData['code'] = Str::random(15);
                $productData['created_by'] = Auth::id();
                $product = Product::create($productData);
                session()->flash('message', 'Producto registrado con éxito.');
            }

            if ($this->productCategories) {
                $product->categories()->sync($this->productCategories);
            }


            $this->searching();
            $this->closeForm();

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            session()->flash('error', 'Hubo un problema al actualizar el producto (' . $errorCode . ') ' . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un problema al actualizar el producto: ' . $e->getMessage());
        }
    }
    private function updateChildProducts($product)
    {
        // Obtener los productos hijos
        $childProducts = Product::where('parent_id', $product->id)->get();

        foreach ($childProducts as $childProduct) {
            $childPurchasePrice = 0;
            $childBasePrice = 0;
            $childFinalPrice = 0;

            if ($product->units_per_package && $product->units_per_package != 0) {
                $childPurchasePrice = $product->purchase_price / $product->units_per_package;
                $childBasePrice = $product->base_price ? $product->base_price / $product->units_per_package : null;
                $childFinalPrice = $product->final_price ? $product->final_price / $product->units_per_package : null;
            }

            $childData = [
                'purchase_price' => $childPurchasePrice,
                'base_price' => $childBasePrice,
                'igv' => $product->igv,
                'final_price' => $childFinalPrice,
                'description' => $product->description,
                'brand_id' => $product->brand_id,
                'sunatcode' => $product->sunatcode,
                'supplier_id' => $product->supplier_id,
                'branch_id' => $product->branch_id,
                'updated_by' => Auth::id()
            ];

            $childProduct->update($childData);

            if ($this->productCategories) {
                $childProduct->categories()->sync($this->productCategories);
            }
        }
    }
    public function createProductChild($productId)
    {
        try {
            $this->save();
            // Obtener el producto padre
            $product = Product::findOrFail($productId);

            // Verificar si units_per_package es null o 0
            if (!$product->units_per_package || $product->units_per_package == 0) {
                session()->flash('error', 'El producto debe tener unidades por paquete válidos para evitar errores en el futuro.');
                return;
            }

            // Calcular los nuevos precios
            $childPurchasePrice = 0;
            $childBasePrice = 0;
            $childFinalPrice = 0;

            if ($product->units_per_package && $product->units_per_package != 0) {
                $childPurchasePrice = $product->purchase_price / $product->units_per_package;
                $childBasePrice = $product->base_price ? $product->base_price / $product->units_per_package : null;
                $childFinalPrice = $product->final_price ? $product->final_price / $product->units_per_package : null;
            }
            // Crear los datos del producto hijo
            $childProductData = [
                'name' => $product->name . ' - Unidad',
                'unit_type' => 1, // Unidad
                'purchase_price' => $childPurchasePrice,
                'units_per_package' => 1, // Unidad
                'base_price' => $childBasePrice,
                'igv' => $product->igv,
                'generic_image_url' => null, // No registra una imagen
                'final_price' => $childFinalPrice,
                'description' => null, // No se pasa la descripción
                'barcode' => null, // No se pasa el código de barras
                'sunatcode' => $product->sunatcode,
                'brand_id' => $product->brand_id,
                'supplier_id' => $product->supplier_id,
                'branch_id' => $product->branch_id,
                'parent_id' => $product->id, // Se asigna el producto padre
                'code' => Str::random(15), // Genera un código único
                'created_by' => Auth::id()
            ];

            // Crear el producto hijo
            $childProduct = Product::create($childProductData);

            // Asignar las mismas categorías del producto padre al producto hijo
            if ($product->categories) {
                $childProduct->categories()->sync($product->categories->pluck('id'));
            }

            session()->flash('message', 'Producto hijo creado con éxito.');
            $this->see($productId);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            session()->flash('error', 'Hubo un problema al crear el producto hijo (' . $errorCode . ') ' . $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un problema al crear el producto hijo: ' . $e->getMessage());
        }
    }
    public function delete($code)
    {
        try {
            // Buscar el producto por su código
            $product = Product::where('code', $code)->firstOrFail();

            // Eliminar el producto
            $product->delete();

            // Mensaje de éxito
            session()->flash('message', 'Producto eliminado con éxito.');
            $this->product_child = null;

        } catch (\Exception $e) {
            // Manejar errores
            session()->flash('error', 'Hubo un problema al eliminar el producto: ' . $e->getMessage());
        }
    }
    protected function duplicateImage($imagePath)
    {
        // Obtén la información del archivo original
        $originalPath = public_path($imagePath);
        $originalExtension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $originalName = pathinfo($originalPath, PATHINFO_FILENAME);

        // Genera un nuevo nombre para la imagen duplicada
        $newFilename = $originalName . '-copia.' . $originalExtension;
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $directory = "uploads/{$currentYear}/{$currentMonth}";

        $newDirectoryPath = public_path($directory);
        $newImagePath = "{$directory}/{$newFilename}";

        // Asegúrate de que el directorio de destino exista
        if (!file_exists($newDirectoryPath)) {
            mkdir($newDirectoryPath, 0755, true);
        }

        // Asegúrate de que el nombre de archivo sea único
        $counter = 1;
        while (file_exists("{$newDirectoryPath}/{$newFilename}")) {
            $newFilename = $originalName . '-copia-' . $counter . '.' . $originalExtension;
            $newImagePath = "{$directory}/{$newFilename}";
            $counter++;
        }

        // Copia el archivo a la nueva ubicación
        copy($originalPath, public_path($newImagePath));

        // Retorna la nueva ruta relativa
        return $newImagePath;
    }
    protected function storeCoverImage($image)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $directory = "uploads/{$currentYear}/{$currentMonth}";

        // Define the full path where the image will be stored
        $path = public_path($directory);

        // Create the directory if it doesn't exist
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $image->getClientOriginalExtension();
        $fullFilename = "{$filename}.{$extension}";
        $counter = 1;

        // Check if file already exists and append counter if it does
        while (file_exists("{$path}/{$fullFilename}")) {
            $fullFilename = "{$filename}-{$counter}.{$extension}";
            $counter++;
        }

        $imgManager = new ImageManager(new Driver());
        $thumbImage = $imgManager->read($image->getRealPath());

        $thumbImage->cover(400, 400);

        // Store the image
        $thumbImage->save("{$path}/{$fullFilename}");

        // Return the relative path
        return "{$directory}/{$fullFilename}";
    }

    /*
    protected function storeCoverImage($image)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $directory = "uploads/{$currentYear}/{$currentMonth}";

        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $image->getClientOriginalExtension();
        $fullFilename = "{$filename}.{$extension}";
        $counter = 1;

        // Check if file already exists and append counter if it does
        while (Storage::exists("public/{$directory}/{$fullFilename}")) {
            $fullFilename = "{$filename}-{$counter}.{$extension}";
            $counter++;
        }

        $imgManager = new ImageManager(new Driver());
        $thumbImage = $imgManager->read($image->getRealPath());

        $thumbImage->cover(400, 400);

        // Store the image
        Storage::put("public/{$directory}/{$fullFilename}", (string) $thumbImage->encode());
        $storedPath = "{$directory}/{$fullFilename}";

        // Remove 'public/' from the stored path
        return str_replace('public/', '', $storedPath);
    }*/
    /*
    protected function storeCoverImage($image)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $directory = "uploads/{$currentYear}/{$currentMonth}";

        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $image->getClientOriginalExtension();

        $fullFilename = "{$filename}.{$extension}";
        $counter = 1;

        // Check if file already exists and append counter if it does
        while (Storage::exists("public/{$directory}/{$fullFilename}")) {
            $fullFilename = "{$filename}-{$counter}.{$extension}";
            $counter++;
        }

        // Store the image and return the path relative to the uploads directory
        $storedPath = $image->storeAs("public/{$directory}", $fullFilename);

        // Remove 'public/' from the stored path
        return str_replace('public/', '', $storedPath);
    }*/
    public function see($productId)
    {
        $product = Product::find($productId);

        if ($product) {
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
        } else {
            session()->flash('error', 'Producto no encontrado.');
        }
    }
    public function deleteImage()
    {
        $this->generic_image_url = null;
        $this->image_path = null;
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
    public function duplicate($productId)
    {
        $product = Product::with('categories')->find($productId);

        if ($product) {
            // Crea una copia del producto
            $newProduct = $product->replicate();
            $newProduct->name .= ' - copia'; // Opcional: Cambia el nombre para distinguirlo

            // Establecer barcode como null para evitar duplicados
            $newProduct->barcode = null;

            // Generar un nuevo código único
            $newProduct->code = Str::random(15); // O usa un método adecuado para generar un código único

            // Duplica la imagen si existe
            if ($product->generic_image_url) {
                $newImagePath = $this->duplicateImage($product->generic_image_url);
                $newProduct->generic_image_url = $newImagePath;
            }

            // Guarda el nuevo producto
            $newProduct->save();

            // Adjunta las categorías al nuevo producto
            $newProduct->categories()->attach($product->categories->pluck('id'));

            // Actualiza la lista de productos
            $this->searching();

            // Opcional: Puedes agregar una notificación o mensaje aquí
            session()->flash('message', 'Producto duplicado exitosamente.');
        } else {
            session()->flash('error', 'Producto no encontrado.');
        }
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


}
