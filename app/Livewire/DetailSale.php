<?php

namespace App\Livewire;

use App\Models\Sale;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Mail\ContactFormSubmission;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Storage;

class DetailSale extends Component
{
    use LivewireAlert;
    public $isDetailOpen = false;
    public $saleId;
    public $details;
    public $paymentMethods;
    public $sale;
    public $selectedDocuments = [];
    public $customerEmail;
    protected $listeners = ["openDetail"];
    public function openDetail($saleId)
    {
        $this->sale = Sale::find($saleId);
        if ($this->sale) {
            $this->details = $this->sale->items()->get();
            $this->paymentMethods = $this->sale->paymentMethods()->get();
            $this->isDetailOpen = true;
        } else {
            return $this->alert('error', 'La Venta No Existe');
        }
    }
    public function render()
    {
        return view('livewire.detail-sale');
    }
    public function sendDocuments()
    {
        if (!in_array(true, $this->selectedDocuments)) {
            // Emitir alerta si ningún documento ha sido seleccionado
            $this->alert('error', 'Debe seleccionar al menos un documento para enviar.');
            return;
        }

        $this->validate([
            'customerEmail' => 'required|email',
        ], [
            'customerEmail.required' => 'El correo electrónico es obligatorio.',
            'customerEmail.email' => 'Por favor ingrese un correo electrónico válido.',
        ]);


        try {
            // Datos del cliente
            $data = [];

            // Documentos seleccionados
            $attachments = [];

            // Recorre los documentos seleccionados
            foreach ($this->selectedDocuments as $type => $isAttached) {
                if ($isAttached) {
                    switch ($type) {
                        case 'signed_xml':
                            $data['info'][] = [
                                'name' => 'XML Firmado',
                                'description' => 'Documento firmado electrónicamente.'
                            ];
                            $attachments[] = $this->generateFilePath($this->sale->signed_xml_path);
                            break;
                        case 'cdr':
                            $data['info'][] = [
                                'name' => 'CDR',
                                'description' => 'Comprobante de Recepción.'
                            ];
                            $attachments[] = $this->generateFilePath($this->sale->cdr_path);
                            break;
                        case 'voucher':
                            $data['info'][] = [
                                'name' => 'Voucher',
                                'description' => 'Comprobante de pago.'
                            ];
                            $attachments[] = $this->generateFilePath($this->sale->document_path);
                            break;
                        case 'voucher_a4':
                            $data['info'][] = [
                                'name' => 'Documento A4',
                                'description' => 'Documento en formato A4.'
                            ];
                            $attachments[] = $this->generateFilePath($this->sale->document_path_oficial);
                            break;
                    }
                }
            }
            
            $data['attachments'] = $attachments;

            // Enviar correo con los documentos adjuntos
            Mail::to($this->customerEmail)->send(new ContactFormSubmission($data));

            $this->alert('success', 'Los documentos han sido enviados con éxito.');
        } catch (\Exception $e) {
            $this->alert('error', 'Ocurrió un error al enviar los documentos.' . $e->getMessage());
        }
    }
    private function generateFilePath($fileName)
    {
        // Ajusta la ruta base donde están almacenados tus archivos en 'public/uploads'
        $basePath = 'uploads/';

        // Retorna la URL completa del archivo
        return public_path($basePath . $fileName);
    }
    public function closeForm()
    {
        $this->saleId = null;
        $this->sale = null;
        $this->details = null;
        $this->paymentMethods = null;
        $this->isDetailOpen = false;
    }
}
