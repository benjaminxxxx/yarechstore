<?php

namespace App\Livewire;

use App\Models\SaleItem;
use Livewire\Component;

class CartChangePriceProduct extends Component
{
    public $isFormOpen = false;
    public $item;
    public $product_price;
    protected $listeners = ['editProductInCart'];
    public function render()
    {
        return view('livewire.cart-change-price-product');
    }
    public function editProductInCart($itemId){
      
        $this->item = SaleItem::find($itemId);
        if($this->item){
            $this->isFormOpen = true;
            $this->product_price = $this->item->product_price;
        }
    }
    public function store(){
      
        if($this->item){
            $quantity = $this->item->quantity;
            $final_price = $this->product_price;
            $igv_percent = $this->item->percent_igv;

            list($subtotal, $totalIGV) = $this->calculateSubtotalAndIGV($final_price, $igv_percent, $quantity);

            $this->item->product_price = $final_price;
            $this->item->subtotal = $subtotal;
            $this->item->total_price = $final_price * $quantity;
            $this->item->igv = $totalIGV;
            $this->item->total_taxes = $totalIGV;

            $this->item->save();
            $this->closeForm();
            $this->dispatch('ProductPriceUpdated',$this->item->id);
        }
    }
    private function calculateSubtotalAndIGV($price, $igv_percent, $quantity)
    {
        $igvRate = $igv_percent/100; // Tasa de IGV para operaciones gravadas

        if ($igv_percent > 0) { // Si es gravado con IGV
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
    public function closeForm(){
        $this->isFormOpen = false;
    }
}
