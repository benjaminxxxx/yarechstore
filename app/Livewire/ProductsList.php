<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Branch;

use Auth;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Str;

class ProductsList extends Component
{
    
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
    public $branch_id;
    
    protected $listeners = ["ProductSaved"=>'$refresh']; 
    public function mount()
    {
        $branchCode = Session::get('selected_branch');
        $branch = Branch::where('code', $branchCode)->first();
        $this->branch_id = $branch ? $branch->id : null;
        $this->products = Product::orderBy('name')->whereNull('parent_id')->get();
        
    }
/*
    public function searching()
    {
        /*
        $branchCode = Session::get('selected_branch');
        $branch = Branch::where('code', $branchCode)->first();

        $this->products = $branch->products()->whereNull('parent_id')
            ->where('branch_id', $this->branch_id) // Filtra por branch_id
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->get();
        
    }*/

    public function render()
    {
        $this->products = Product::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })->where('is_active',true)->orderBy('name')->get();

        return view('livewire.products-list');
    }
    /*
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
    }*/
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

    public function see($productId)
    {
        $this->dispatch('viewProduct',$productId);
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

    


}
