<?php

namespace App\Livewire;

use App\Models\CashTransaction;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Unit;
use Livewire\Component;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Auth;
use Session;
use Illuminate\Support\Facades\DB;

class Sell extends Component
{
    use LivewireAlert;
    public $branch;
    public $branchCode;
    public $branches;
    public $searchProduct = '';
    public $searchCustomer = '';
    public $resultsProduct;
    public $currentSale;
    public $sales = [];
    public $currentIndex = 0;
    public $quantities = [];
    public $isCashRegisterEnabled;
    public $codigoVenta;
    protected $listeners = ['saleProcessed', 'ProductPriceUpdated', 'anularVenta'];
    public function mount()
    {
        $this->isCashRegisterEnabled = env('USE_CASH_REGISTER', false);
        $this->branches = Auth::user()->branches;
        $this->branchCode = Session::get('selected_branch');
        $this->resultsProduct = collect();
        if ($this->branchCode) {
            $this->branch = Branch::where('code', $this->branchCode)->first();
            $this->reCart();
        }
    }
    
    public function reCart()
    {
        if ($this->branch) {


            if ($this->isCashRegisterEnabled) {
                $cashRegister = $this->branch->cashRegisterOpen;
                if ($cashRegister) {
                    $this->sales = $cashRegister->sales()->orderBy('created_at', 'desc')->get();
                }
            } else {
                $this->sales = $this->branch->sales()->orderBy('created_at', 'desc')->get();
            }


            if ($this->sales->isNotEmpty()) {
                $this->currentIndex = 0;
                $this->currentSale = $this->sales[$this->currentIndex];
                if ($this->currentSale) {
                    foreach ($this->currentSale->items as $item) {
                        $this->quantities[$item->id] = $item->quantity;
                    }
                }
            } else {
                $this->currentIndex = 0;
                $this->currentSale = null;
            }
        }
    }

    public function updatedSearchProduct()
    {
        if ($this->branch) {
            /*$this->resultsProduct = $this->branch->products()
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->searchProduct . '%')
                        ->orWhere('description', 'like', '%' . $this->searchProduct . '%');
                })
                ->whereHas('inventories', function ($query) {
                    $query->where('stock', '>', 0);
                })
                ->with('inventories')
                ->get();
                */
            $this->resultsProduct = Product::where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchProduct . '%')
                    ->orWhere('description', 'like', '%' . $this->searchProduct . '%');
            })->where('is_active',true)->get();
        }
    }
    public function addCart()
    {
        // Crea una nueva venta con estado 0 (en carrito)
        try {
            // Crear una nueva venta con estado 'cart'
            $data = [
                'code' => Str::random(15),
                'status' => 'cart',
                'customer_id' => null, // O establece un cliente predeterminado si es necesario
                'subtotal' => 0,
                'total' => 0, // Inicialmente, la venta no tiene productos, por lo que el total es 0
                'igv' => 0, // Ajusta el IGV según sea necesario
                'document_status' => 0, // Ajusta según los estados que tengas
                'document_number' => null,
                'branch_id' => $this->branch->id
            ];

            $cashRegister = $this->branch->cashRegisterOpen;

            if ($this->isCashRegisterEnabled && $cashRegister) {
                $data['cash_register_id'] = $cashRegister->id;
            }

            $this->currentSale = Sale::create($data);
            
            $this->reCart();

        } catch (QueryException $e) {
            Session::flash('error', 'Hubo un error al agregar la venta. Por favor, inténtelo nuevamente:' . $e->getMessage());
        }
    }
    public function addToCart($code, $unit = 1, $factor = 1, $price_in_factor = null)
    {
        $product = $this->getProduct($code);
        if (!$product)
            return $this->alert('error', 'El producto ya no existe');

        $stockInBranch = $product->stocks->where('branch_id', $this->branch->id)->where('stock', '>', 0)->first();

        if (!$stockInBranch) {

            $stockInOtherBranches = $product->stocks->where('stock', '>', 0)->where('branch_id', '!=', $this->branch->id);
            if ($stockInOtherBranches->count() > 0) {
                return $this->alert('error', 'Hay Stock en otras Sucursales');
            }
            return $this->alert('error', 'No hay Stock del producto');
        }

        if (!$this->currentSale || $this->currentSale->status != 'cart') {
            
            $this->addCart();
        }
        
        if ($this->currentSale) {

            // Obtener todos los ítems en el carrito para este producto (sin importar la unidad)
            $itemsInCart = $this->currentSale->items()->where('product_id', $product->id)->get();

            // Sumar la cantidad total de este producto en el carrito, considerando el factor de cada ítem
            $currentTotalQuantityInCart = $itemsInCart->sum(function ($item) {
                return $item->quantity * $item->factor;
            });

            // Calcular la cantidad total requerida con la nueva adición
            $totalRequiredQuantity = $currentTotalQuantityInCart + ($factor * 1);

            // Validar si la cantidad total requerida supera el stock disponible
            if ($totalRequiredQuantity > $stockInBranch->stock) {
                return $this->alert('error', 'La cantidad de productos que hay en el empaque supera el stock disponible');
            }


            $existingItem = $this->currentSale->items()->where('product_id', $product->id)->where('unit', $unit)->first();

            $final_price = $price_in_factor ? $price_in_factor : $product->final_price;

            if ($existingItem) {
                $this->addQuantityToCart($existingItem->id);
            } else {
                // Si el producto no está en el carrito, lo agregamos como un nuevo item
                $igv_percent = $product->igv==1?18:0;
                list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($final_price, $igv_percent, 1);

                $sufix = '';
                if ($unit != 1) {
                    $namesufix = Unit::find($unit);
                    if ($namesufix) {
                        $sufix = ' ' . $namesufix->name . ' x' . $factor;
                    }
                }

                $product_name = $product->name . $sufix;

                $newItem = $this->currentSale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product_name,
                    'product_price' => $final_price,
                    // Calcular el subtotal sin IGV
                    'subtotal' => $subtotal,
                    'unit_value' => (float) $product->igv == 2 ? $final_price : $final_price / 1.18,
                    'total_taxes' => (float) $totalIGV,
                    'percent_igv' => (float) ($product->igv == 2 ? 0 : 18),
                    'igv' => $totalIGV,
                    'quantity' => 1,
                    'total_price' => $final_price,
                    'unit' => $unit,
                    'factor' => $factor,
                ]);

                // Agregar el nuevo ítem al array de cantidades
                $this->quantities[$newItem->id] = 1;
                $this->reCart();
            }

            // Actualizar el total de la venta
            $this->updateSaleTotal();
        }
    }
    public function removeQuantityToCart($itemId)
    {
        $item = $this->currentSale->items()->find($itemId);
        if ($item) {
            if ($item->quantity > 1) {
                $item->quantity -= 1;
                $item->total_price = $item->product_price * $item->quantity;

                list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($item->product_price, $item->percent_igv, $item->quantity);

                $item->total_price = $item->product_price * $item->quantity;
                $item->subtotal = $subtotal;
                $item->igv = $totalIGV;
                $item->total_taxes = $totalIGV;
                $item->save();

                $this->quantities[$itemId] = $item->quantity;
            } else {
                $item->delete();
                unset($this->quantities[$itemId]);
            }
            $this->updateSaleTotal();
        }
    }

    public function addQuantityToCart($itemId)
    {
        /* $item = $this->currentSale->items()->find($itemId);
         if ($item) {
             $inventory = Inventory::where('product_id', $item->product_id)->first();
             $stockAvailable = $inventory ? $inventory->stock : null;

             if (is_null($stockAvailable) || $stockAvailable > $item->quantity) {
                 $item->quantity += 1;

                 list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($item->product_price, $item->product_price, $item->quantity);
                 $item->total_price = $item->product_price * $item->quantity;
                 $item->subtotal = $subtotal;
                 $item->igv = $totalIGV;
                 $item->total_taxes = $totalIGV;
                 $item->save();
                 $this->quantities[$itemId] = $item->quantity;
                 $this->updateSaleTotal();
             }
         }*/
        $item = $this->currentSale->items()->find($itemId);

        if ($item) {
            // Obtener el producto
            $product = $item->product;

            $itemsInCart = $this->currentSale->items()->where('product_id', $item->product_id)->get();
            $currentTotalQuantityInCart = $itemsInCart->sum(function ($item) {
                return $item->quantity * $item->factor;
            });

            // Obtener el stock del producto en la sucursal actual
            $stockInBranch = $product->stocks->where('branch_id', $this->branch->id)->first();

            // Verificar si el stock está disponible
            $stockAvailable = $stockInBranch ? $stockInBranch->stock : 0;
            $totalRequiredQuantity = $currentTotalQuantityInCart + ($item->factor * 1);


            // Verificar si hay suficiente stock para agregar al carrito
            if ($stockAvailable > $totalRequiredQuantity) {
                $item->quantity += 1;

                list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($item->product_price, $item->percent_igv, $item->quantity);
              
                $item->total_price = $item->product_price * $item->quantity;
                $item->subtotal = $subtotal;
                $item->igv = $totalIGV;
                $item->total_taxes = $totalIGV;
                $item->save();

                $this->quantities[$itemId] = $item->quantity;
                $this->updateSaleTotal();
            } else {
                $this->alert('error', 'No hay suficiente stock disponible en la sucursal actual.');
            }
        }
    }


    public function updateQuantityToCart($itemId)
    {
        $quantity = $this->quantities[$itemId] ?? 0;

        if ($quantity <= 0) {
            $this->removeQuantityToCart($itemId);
            return;
        }

        $item = $this->currentSale->items()->find($itemId);
        if ($item) {

            list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($item->product_price, $item->product_price, $quantity);

            $item->quantity = $quantity;
            $item->total_price = $item->product_price * $quantity;
            $item->subtotal = $subtotal;
            $item->igv = $totalIGV;
            $item->total_taxes = $totalIGV;
            $item->save();

            // Actualiza el subtotal y el IGV
            $this->updateSaleTotal();
        }
    }
    private function calculateSubtotalAndIGV($price, $igv_percent, $quantity)
    {
        $igvRate = $igv_percent / 100; // Tasa de IGV
    
        // Precio total sin considerar si tiene o no IGV
        $totalPrice = round($price * $quantity, 2);
    
        if ($igv_percent > 0) { // Si es gravado con IGV
            // Calcular el monto de IGV desde el total
            $totalIGV = round($totalPrice * $igvRate / (1 + $igvRate), 2);
    
            // Calcular el subtotal como el total menos el IGV
            $subtotal = round($totalPrice - $totalIGV, 2);
        } else { // Si es exonerado
            $subtotal = $totalPrice;
            $totalIGV = 0.00;
        }
    
        return [$subtotal, $totalIGV];
    }
    protected function updateSaleTotal()
    {
        $total = $this->currentSale->items->sum(function ($item) {
            return $item->product_price * $item->quantity;
        });

        $subtotalwithoutigv = $this->currentSale->items->sum(function ($item) {
            // Calcular el precio sin IGV basado en el porcentaje de IGV del producto
            $percentage_factor = 1 + ($item->percent_igv / 100);
            return ($item->percent_igv == 0) ? $item->product_price * $item->quantity : ($item->product_price / $percentage_factor) * $item->quantity;
        });

        $this->currentSale->update([
            'total_amount' => $total,
            'subtotal' => $subtotalwithoutigv,
            'igv' => $total - $subtotalwithoutigv,
        ]);
    }
    public function removeToCart($itemId)
    {
        try {

            $item = SaleItem::find($itemId);

            if ($item) {

                $item->delete();
                $this->updateSaleTotal();

            } else {
                session()->flash('error', 'El producto no existe en el carrito.');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al eliminar el producto del carrito: ' . $e->getMessage());
        }
    }

    public function getProduct($code)
    {

        $product = null;

        if ($this->branch) {
            $product = Product::where('code', $code)->first();
        }

        return $product;
    }
    public function removeCart($code)
    {
        try {
            // Buscar la venta por su código
            $sale = Sale::where('code', $code)->where('status', 'cart')->first();

            if ($sale) {
                // Eliminar la venta
                $sale->delete();
                $this->reCart();
            } else {
                // Opcional: Mensaje si no se encuentra la venta
                session()->flash('error', 'Venta no encontrada.');
            }
        } catch (\Exception $e) {
            // Manejo de excepciones
            session()->flash('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }
    public function confirmarAnularVenta($codigoVenta)
    {
        $this->codigoVenta = $codigoVenta;

        $this->alert('question', '¿Está seguro(a) que desea Anular esta Venta?, los productos se devolver a Stock', [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Si, Anular',
            'onConfirmed' => 'anularVenta',
            'showCancelButton' => true,
            'position' => 'center',
            'toast' => false,
            'timer' => null,
            'confirmButtonColor' => '#F5922A', // Esto sobrescribiría la configuración global
            'cancelButtonColor' => '#2C2C2C',
        ]);
    }
    public function anularVenta()
    {
     
        if (!$this->codigoVenta) {
            return;
        }

        try {
            // Iniciar una transacción
            DB::transaction(function () {
                // Buscar la venta por su código
                $sale = Sale::where('code', $this->codigoVenta)->where('status', 'paid')->first();
                $branch = $this->branch;

                if ($sale && $branch) {
                    // Cambiar el estado de la venta a 'canceled'
                    $sale->status = 'canceled';
                    $sale->save(); // Guarda el estado de la venta

                    $cashRegister = $this->branch->cashRegisterOpen;
                    if ($cashRegister) {
                        CashTransaction::create([
                            'cash_register_id' => $cashRegister->id,
                            'amount' => $sale->total_amount,
                            'sale_id' => $sale->id,
                            'type' => 'refund', 
                            'description' => 'Venta anulada',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Restablecer el stock en product_stocks
                    foreach ($sale->items as $item) {
                        $product = $item->product;

                        // Obtener el stock del producto en la sucursal actual
                        $stockInBranch = $product->stocks()->where('branch_id', $branch->id)->first();

                        if ($stockInBranch) {
                            // Actualizar el stock existente
                            $stockInBranch->stock += $item->quantity * $item->factor;
                            $stockInBranch->save(); // Guarda el nuevo stock
                        } else {
                            // Crear un nuevo registro de stock si no existe
                            ProductStock::create([
                                'branch_id' => $branch->id,
                                'product_id' => $product->id,
                                'stock' => $item->quantity * $item->factor,
                                'minimum_stock' => 0,
                                'price' => $product->price, // Asignar el precio del producto si es necesario
                            ]);
                        }
                    }

                    $this->alert('success', 'Venta Anulada con Éxito');
                    $this->reCart();
                } else {
                    // Opcional: Mensaje si no se encuentra la venta
                    $this->alert('error', 'Venta no encontrada.');
                }
            });
        } catch (\Exception $e) {
            // Manejo de excepciones
            $this->alert('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }
    public function prevSale()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->currentSale = $this->sales[$this->currentIndex];
        }
    }

    public function nextSale()
    {
        if ($this->currentIndex < $this->sales->count() - 1) {
            $this->currentIndex++;
            $this->currentSale = $this->sales[$this->currentIndex];
        }
    }
    

    public function saleProcessed()
    {
        $this->reCart();
    }
    public function ProductPriceUpdated($itemId)
    {
        //$this->updateQuantityToCart($itemId);
        $this->updateSaleTotal();
        $this->reCart();
    }

    public function render()
    {
        return view('livewire.sell', [
            'showPrevButton' => $this->currentIndex > 0,
            'showNextButton' => $this->currentIndex < $this->sales->count() - 1,
        ]);
    }
}
