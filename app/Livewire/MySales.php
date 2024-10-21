<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Sale;
use App\Services\GenerateDocumentService;
use App\Services\GenerateInvoiceSunatService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Session;

class MySales extends Component
{
    use WithPagination;
    use LivewireAlert;
    public $branchCode;
    public $branch;
    public function render()
    {
        $this->branchCode = Session::get('selected_branch');
        if ($this->branchCode) {
            $this->branch = Branch::where('code', $this->branchCode)->first();
        }

        $sales = null;

        if ($this->branch) {
            $sales = $this->branch->sales()->orderBy('created_at', 'desc')->paginate(10);
        }
        return view('livewire.my-sales', [
            'sales' => $sales
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
    public function openDetailOption($saleId)
    {

        $this->dispatch('openDetail', $saleId);
    }
    public function generateNewVoucher($saleId)
    {

        try {
            $sale = Sale::find($saleId);
            if($sale){
                $generateInvoiceService = new GenerateInvoiceSunatService();

                $options = [
                    'regenerate' => true,
                ];

                $response = $generateInvoiceService->process($sale,$options);
                if(!$response['status']){
                    throw new Exception($response['message'], 1);                    
                }
            }
            //$generateDocumentService = new GenerateDocumentService();
            //$generateDocumentService->createSimpleVoucher($saleId);

            $this->alert('success', 'Voucher generado correctamente');

        } catch (\Throwable $th) {
            $this->alert('error', 'Ocurrió un error al generar el Voucher: ' . $th->getMessage());
        }
    }
}
