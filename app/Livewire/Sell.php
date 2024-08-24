<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
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
            $this->resultsProduct = $this->branch->products()
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->searchProduct . '%')
                        ->orWhere('description', 'like', '%' . $this->searchProduct . '%');
                })
                ->whereHas('inventories', function ($query) {
                    $query->where('stock', '>', 0);
                })
                ->with('inventories')
                ->get();
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
    public function addToCart($code)
    {
        $product = $this->getProduct($code);
        if (!$product)
            return;

        if (!$this->currentSale || $this->currentSale->status != 'cart') {
            $this->addCart();
        }

        if ($this->currentSale) {

            $existingItem = $this->currentSale->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $this->addQuantityToCart($existingItem->id);
            } else {
                // Si el producto no está en el carrito, lo agregamos como un nuevo item

                list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($product->final_price, $product->igv, 1);

                $newItem = $this->currentSale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->final_price,
                    // Calcular el subtotal sin IGV
                    'subtotal' => $subtotal,
                    'unit_value' => (float) $product->igv == 2 ? $product->final_price : $product->final_price / 1.18,
                    'total_taxes' => (float) $totalIGV,
                    'percent_igv' => (float) ($product->igv == 2 ? 0 : 18),
                    'igv' => $totalIGV,
                    'quantity' => 1,
                    'total_price' => $product->final_price,
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

                list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($item->product_price, $item->product_price, $item->quantity);

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
        $item = $this->currentSale->items()->find($itemId);
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
    private function calculateSubtotalAndIGV($price, $igv, $quantity)
    {
        $igvRate = 0.18; // Tasa de IGV para operaciones gravadas

        if ($igv == 1) { // Si es gravado con IGV
            // Calcular el precio sin IGV
            $priceWithoutIGV = round($price / (1 + $igvRate), 2);

            // Calcular el subtotal sin IGV
            $subtotal = round($priceWithoutIGV * $quantity, 2);

            // Calcular el total IGV basado en el subtotal
            $totalIGV = round($subtotal * $igvRate, 2);
        } else { // Si es exonerado
            $priceWithoutIGV = $price;
            $subtotal = round($priceWithoutIGV * $quantity, 2);
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
            return ($item->percent_igv == 0) ? 0 : ($item->product_price / 1.18);
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
            $product = $this->branch->products()->where('code', $code)->first();
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
            'confirmButtonColor' => '#4F46E5', // Esto sobrescribiría la configuración global
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

                    // Restablecer el stock en el inventario
                    foreach ($sale->items as $item) {
                        $product = $item->product;
                        $inventory = $product->inventory;

                        if ($inventory) {
                            $inventory->stock += $item->quantity;
                            $inventory->save(); // Guarda el nuevo stock
                        } else {
                            // Crear un nuevo registro de inventario si no existe
                            Inventory::create([
                                'branch_id' => $branch->id,
                                'product_id' => $product->id,
                                'stock' => $item->quantity,
                                'minimum_stock' => 0,
                                'location' => null,
                                'expiry_date' => null,
                            ]);
                        }
                    }

                    $this->alert('success', 'Venta Anulada con Exito');
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
    protected $listeners = ['saleProcessed', 'ProductPriceUpdated', 'anularVenta'];

    public function saleProcessed()
    {
        $this->reCart();
    }
    public function ProductPriceUpdated($itemId)
    {
        $this->updateQuantityToCart($itemId);
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
