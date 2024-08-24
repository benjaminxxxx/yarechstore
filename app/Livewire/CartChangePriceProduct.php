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
            $this->item->product_price = $this->product_price;
            $this->item->save();
            $this->closeForm();
            $this->dispatch('ProductPriceUpdated',$this->item->id);
        }
    }
    public function closeForm(){
        $this->isFormOpen = false;
    }
}
