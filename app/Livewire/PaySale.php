<?php

namespace App\Livewire;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Sale;
use App\Models\PaymentMethod;
use App\Models\CashTransaction;
use App\Models\DigitalTransaction;
use App\Models\Branch;
use App\Models\InvoicesType;
use Session;
use DB;
use App\Services\GenerateInvoiceSunatService;

class PaySale extends Component
{
    use LivewireAlert;
    public $saleCode;
    public $isFormPayOpen = false;
    public $methods = [];
    public $methodsAdded = [];
    public $selectedMethod = null;
    public $subtotal;
    public $change;
    public $isPaymentMethodAvailable = false;
    public $total_amount = 0;
    public $branch;
    public $document_selected = 'recibo';
    public $isCashRegisterEnabled;
    public $no_register_change;
    public $currentSale;

    public function mount()
    {
        $this->methods = [
            'cash' => ['icon' => 'fa-money-bill', 'label' => 'Efectivo', 'amount' => '0', 'pretty-amount' => 'S/. 0.00'],
            'card' => ['icon' => 'fa-credit-card', 'label' => 'Tarjeta', 'amount' => '0', 'pretty-amount' => 'S/. 0.00'],
            'yape' => ['icon' => 'fa-mobile-alt', 'label' => 'Yape', 'amount' => '0', 'pretty-amount' => 'S/. 0.00'],
            'plin' => ['icon' => 'fa-qrcode', 'label' => 'Plin', 'amount' => '0', 'pretty-amount' => 'S/. 0.00'],
            'client' => ['icon' => 'fa-user', 'label' => 'Cuenta Cliente', 'amount' => '0', 'pretty-amount' => 'S/. 0.00'],
        ];
        $this->branchCode = Session::get('selected_branch');
        if ($this->branchCode) {
            $this->branch = Branch::where('code', $this->branchCode)->first();
        }
        $this->document_selected = 'recibo';
        $this->isCashRegisterEnabled = env('USE_CASH_REGISTER', false);
        $this->currentSale = Sale::where('code', $this->saleCode)->first();
    }

    public function addMethod($methodId)
    {
        if (!$this->isMethodAdded($methodId)) {
            if (isset($this->methods[$methodId])) {
                $this->methodsAdded[$methodId] = $this->methods[$methodId];
                $this->selectMethod($methodId);
            }
        } else {
            $this->selectMethod($methodId);
        }
    }
    private function isMethodAdded($methodId)
    {
        return array_key_exists($methodId, $this->methodsAdded);
    }
    public function selectMethod($id)
    {
        $this->selectedMethod = $id;
        $this->dispatch('focus-input');
    }
    public function removeMethod($id)
    {
        if (isset($this->methodsAdded[$id])) {
            unset($this->methodsAdded[$id]);

            if ($this->selectedMethod === $id) {
                $this->selectedMethod = count($this->methodsAdded) > 0
                    ? array_key_first($this->methodsAdded)
                    : null;
            }
            $this->recalculateChange();

        }
    }

    public function updated($propertyName)
    {
        // Detecta cuando se actualiza un valor específico en el array methodsAdded
        if (str_starts_with($propertyName, 'methodsAdded.')) {
            $this->recalculateChange();
        }
    }

    /**
     * Formatea un valor numérico en formato decimal (##,#00.00).
     */
    private function formatAmount($amount)
    {
        // Convertir el valor a un número flotante
        $numericAmount = floatval($amount);

        // Formatear el número con dos decimales
        return 'S/. ' . number_format($numericAmount, 2, '.', ',');
    }
    public function recalculateChange()
    {
        $totalPaid = array_sum(
            array_map(function ($method) {
                return !empty($method['amount']) ? (float) $method['amount'] : 0;
            }, $this->methodsAdded)
        );

        $this->change = $totalPaid - $this->subtotal;
        $this->checkMethodAvailable();
    }
    public function pay()
    {
        $this->methodsAdded = [];

        $sale = Sale::where('code', $this->saleCode)->first();

        // Verificar si se encontró la venta
        if ($sale) {
            if ($sale->total_amount == 0) {
                return session()->flash('error', 'Agregue productos al carrito');
            }
            // Establecer isFormPayOpen en true para abrir el formulario de pago
            $this->isFormPayOpen = true;
            $this->dispatch("loadChart");

            // Actualizar el subtotal con el monto total de la venta
            $this->subtotal = $sale->total_amount;
            $this->addMethod('cash');
            $this->dispatch('saleUpdated');
        } else {
            // Si no se encuentra la venta, manejar el caso (por ejemplo, mostrar un mensaje de error)
            session()->flash('error', 'No se encontró una venta con ese código.');
        }
    }
    public function backStep()
    {
        $this->isFormPayOpen = false;
        $this->dispatch('saleUpdated');
        $this->change = 0;
        $this->methodsAdded = [];

    }

    public function checkMethodAvailable()
    {
        // Verifica si hay al menos un método de pago agregado con un monto mayor a 0
        $this->isPaymentMethodAvailable = collect($this->methodsAdded)->contains(function ($method) {
            return $method['amount'] > 0;
        });
    }
    public function processSale()
    {
        $verification = $this->verifyInventory();

        if (!$verification['success']) {
            // Mostrar mensaje de error
            return $this->alert('error', $verification['message']);
        }

        if ($this->change < 0) {
            return $this->alert('error', "No se puede hacer descuentos por este medio, regrese al carrito y haga el descuento allí");
        }

        if (!$this->document_selected) {
            return $this->alert('error', "Seleccione un tipo de comprobante");
        }

        if (array_key_exists('client', $this->methodsAdded) && $this->methodsAdded['client']['amount'] > 0) {
            if (!$this->existsClient()) {
                return $this->alert('error', "Busque o Agregue un Cliente para Guardar el Pago Pendiente");
            }
            
        }
        if ($this->document_selected == 'factura') {
            //solicitar datos del cliente
            if (!$this->existsClient()) {
                return $this->alert('error', "Seleccione o agrega un cliente");
            }

            if(!$this->clientWithFactura()){
                return $this->alert('error', "Para factura el cliente debe tener un RUC de 11 digitos");
            }

            //verificar si tiene correlativo
            if (!$this->existsCorrelative("factura")) {
                return $this->alert('error', "Configure el correlativo de la Factura");
            }
        }
        if ($this->document_selected == 'boleta') {

            //verificar si tiene correlativo
            if (!$this->existsCorrelative("boleta")) {
                return $this->alert('error', "Configure el correlativo de la Boleta");
            }

            if ((float) $this->currentSale->total_amount > 700 && $this->currentSale->customer == null) {
                return $this->alert('error', "Para emitir boletas mayores a S/. 700.00 soles debe agregar un cliente");
            }
        }

        $sale = Sale::where('code', $this->saleCode)->first();
        if (!$sale) {
            return $this->alert('error', "La venta dejó de existir");
        }

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Inicializar variables para el monto total recibido y el cambio
            $totalReceived = 0;
            $change = $this->change;

            // Actualizar inventario
            /*foreach ($sale->items as $item) {
                $inventory = $item->product->inventory;

                // Verificar si hay inventario
                if ($inventory && $inventory->stock >= $item->quantity) {
                    $inventory->decrement('stock', $item->quantity);
                } else {
                    // Si falla, hacer rollback y mostrar mensaje de error
                    DB::rollBack();
                    return session()->flash('error', 'No hay suficiente stock para el producto: ' . $item->product->name);
                }
            }*/
            foreach ($sale->items as $item) {
                $product = $item->product;

                // Obtener el stock del producto en la sucursal actual
                $stockInBranch = $product->stocks()->where('branch_id', $this->branch->id)->first();

                // Verificar si hay inventario suficiente considerando el factor
                if ($stockInBranch && $stockInBranch->stock >= $item->quantity * $item->factor) {
                    // Decrementar el stock considerando el factor
                    $stockInBranch->decrement('stock', $item->quantity * $item->factor);
                } else {
                    // Si falla, hacer rollback y mostrar mensaje de error
                    DB::rollBack();
                    return $this->alert('error', 'No hay suficiente stock para el producto: ' . $product->name);
                }
            }

            // Cambiar el estado de la venta según el método de pago
            $saleStatus = 'paid';  // Estado por defecto

            if (array_key_exists('client', $this->methodsAdded) && $this->methodsAdded['client']['amount'] > 0) {
                $saleStatus = 'debt';  // Cambiar el estado a deuda si 'client' tiene un monto mayor a 0
            }

            $invoiceTypeId = null;

            if ($this->document_selected == 'boleta') {
                $InvoicesType = InvoicesType::where('code', '03')->first();
                if ($InvoicesType) {
                    $invoiceTypeId = $InvoicesType->id;
                }
            }
            if ($this->document_selected == 'factura') {
                $InvoicesType = InvoicesType::where('code', '01')->first();
                if ($InvoicesType) {
                    $invoiceTypeId = $InvoicesType->id;
                }
            }
            $sale->update([
                'status' => $saleStatus,
                'invoice_type_id' => $invoiceTypeId,
                'cash' => $change,
                'total_payed' => collect($this->methodsAdded)->sum('amount')
            ]);
            PaymentMethod::where('sale_id', $sale->id)->delete();
            // Registrar los métodos de pago en payment_methods
            foreach ($this->methodsAdded as $method => $detail) {
                PaymentMethod::create([
                    'sale_id' => $sale->id,
                    'method' => $method,
                    'amount' => $detail['amount']
                ]);

                if ($method === 'cash') {
                    $totalReceived = $detail['amount']; // Monto recibido en efectivo
                }
            }
            $this->isCashRegisterEnabled = env('USE_CASH_REGISTER', false);
            if ($this->isCashRegisterEnabled) {



                $cashRegister = $this->branch->cashRegisterOpen;  // Obtener la caja abierta
                DigitalTransaction::where('sale_id', $sale->id)->delete();


                if ($cashRegister) {

                    $sale->cash_register_id = $cashRegister->id;
                    $sale->save();

                    // Agregar el efectivo recibido
                    $cashRegister->increment('current_amount', $totalReceived);
                    CashTransaction::where('cash_register_id', $cashRegister->id)->where('sale_id', $sale->id)->delete();

                    CashTransaction::create([
                        'cash_register_id' => $cashRegister->id,
                        'amount' => $totalReceived,
                        'sale_id' => $sale->id,
                        'type' => 'sale', // Tipo de transacción: gasto
                        'description' => 'Venta realizada',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Registrar el cambio como egreso

                    if ($change > 0) {
                        if ($this->no_register_change) {
                            DigitalTransaction::create([
                                'sale_id' => $sale->id,
                                'amount' => $change,
                                'type' => 'expense', // Tipo de transacción: gasto
                                'description' => 'Cambio entregado'
                            ]);
                        } else {
                            CashTransaction::create([
                                'cash_register_id' => $cashRegister->id,
                                'amount' => $change,
                                'sale_id' => $sale->id,
                                'type' => 'expense', // Tipo de transacción: gasto
                                'description' => 'Cambio entregado',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                    }
                }
            }

            // Confirmar la transacción
            DB::commit();

            // Mostrar mensaje de éxito
            $this->alert('success', 'Venta procesada exitosamente.');
            $this->generateInvoice($sale);
            $this->backStep();
            $this->dispatch('saleProcessed');

        } catch (\Exception $e) {
            // Si algo falla, hacer rollback
            DB::rollBack();

            // Mostrar mensaje de error
            return $this->alert('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }
    public function existsClient()
    {
        if ($this->currentSale) {
            return $this->currentSale->customer_id != null;
        }
    }
    public function clientWithFactura()
    {
        if ($this->currentSale) {
            $documentoLength = strlen(trim($this->currentSale->customer->document_number));
            $documentoType = strlen(trim($this->currentSale->customer->document_type_id));
            return $documentoType=='1' && $documentoLength==11;
        }
        return false;
    }
    public function existsCorrelative($type)
    {
        // Verifica si la sucursal está definida
        if (!$this->branch) {
            return false; // No hay sucursal, por lo que no puede haber correlativos
        }

        // Obtiene los correlativos de la sucursal
        $correlatives = $this->branch->correlatives();

        // Verifica si se obtuvieron correlativos
        if (!$correlatives) {
            return false; // No se encontraron correlativos
        }

        // Define el tipo de correlativo según el tipo
        $invoiceTypeCode = null;
        switch ($type) {
            case 'factura':
                $invoiceTypeCode = '01';
                break;
            case 'boleta':
                $invoiceTypeCode = '03'; // Asumiendo que 'boleta' corresponde a un ID específico
                break;
            default:
                return false; // Tipo desconocido
        }
        $InvoicesType = InvoicesType::where('code', $invoiceTypeCode)->first();
        if (!$InvoicesType) {
            return false;
        }

        $invoiceTypeId = $InvoicesType->id;
        // Verifica si existe un correlativo correspondiente al tipo definido
        return $correlatives->where('invoice_type_id', $invoiceTypeId)->exists();
    }

    public function generateInvoice($sale)
    {
        $generateInvoiceService = new GenerateInvoiceSunatService();
       
        $response = $generateInvoiceService->process($sale);
        if(array_key_exists('sunatResponse',$response)){
            if($response['sunatResponse']['success']==false){
                return $this->alert('error',$response['sunatResponse']['error']['code'] . ' - ' . $response['sunatResponse']['error']['message']);
            }
        }
        
        //dd($response);
    }
    /*public function verifyInventory()
    {
        $sale = Sale::where('code', $this->saleCode)->first();

        foreach ($sale->items as $item) {
            $product = $item->product;
            $inventory = $product->inventory;

            if ($inventory) {

                if ($inventory->stock < $item->quantity) {
                    return [
                        'success' => false,
                        'message' => "Insufficient inventory for product: {$product->name} (ID: {$product->id})"
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => "No inventory record for product: {$product->name} (ID: {$product->id})"
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'All products have sufficient inventory.'
        ];
    }*/
    public function verifyInventory()
    {
        $sale = Sale::where('code', $this->saleCode)->first();

        foreach ($sale->items as $item) {
            $product = $item->product;
            $stockInBranch = $product->stocks()->where('branch_id', $this->branch->id)->first();

            if ($stockInBranch) {
                // Verificar si el inventario es suficiente considerando el factor
                if ($stockInBranch->stock < $item->quantity * $item->factor) {
                    return [
                        'success' => false,
                        'message' => "Inventario insuficiente para el producto: {$product->name} (ID: {$product->id})"
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => "No existe registro de inventario para el producto: {$product->name} (ID: {$product->id})"
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Todos los productos tienen inventario suficiente.'
        ];
    }

    public function render()
    {
        return view('livewire.pay-sale');
    }
}
