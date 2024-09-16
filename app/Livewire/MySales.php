<?php

namespace App\Livewire;

use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;
use Session;
use Storage;

class MySales extends Component
{
    use WithPagination;
    public $branchCode;
    public $branch;
    public function render()
    {
        $this->branchCode = Session::get('selected_branch');
        if ($this->branchCode) {
            $this->branch = Branch::where('code', $this->branchCode)->first();
        }

        $sales = null;

        if($this->branch){
            $sales = $this->branch->sales()->orderBy('created_at','desc')->paginate(10);
        }
        return view('livewire.my-sales',[
            'sales'=>$sales
        ]);
    }
    public function downloadXML($path)
    {
        return Storage::disk('public')->download($path);
    }
    public function downloadCDR($path)
    {
        return Storage::disk('public')->download($path);
    }
    public function downloadDocument($path)
    {
        return Storage::disk('public')->download($path);
    }
    public function openDetailOption($saleId){
      
        $this->dispatch('openDetail',$saleId);
    }
}
