<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Branch;

use Auth;
use Illuminate\Support\Facades\Session;
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


    public function render()
    {
        $this->products = Product::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })->where('is_active',true)->orderBy('name')->get();

        return view('livewire.products-list');
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
    
    public function create(){
        $this->dispatch('createNewProduct');
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
