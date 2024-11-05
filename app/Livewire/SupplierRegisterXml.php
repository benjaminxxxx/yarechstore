<?php

namespace App\Livewire;

use App\Models\Supplier as Company;
use App\Models\Company as Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Ramsey\Uuid\Type\Decimal;
use SimpleXMLElement;
use Str;

class SupplierRegisterXml extends Component
{
    //<!--REGISTERXML-->
    use WithFileUploads;
    use LivewireAlert;
    public $xml_file;
    public $thereAreCompanies = false;
    public $customer;

    public $name;
    public $ruc;
    public $contact_person;
    public $phone;
    public $email;
    public $address;
    public $whatsapp;

    public $purchases;
    public $purchase;
    public $purchaseDetail;
    public $isFormOpen = false;

    protected $listeners = ['registerCompany'];
    public function mount()
    {
        $this->thereAreCompanies = Company::where('user_id', Auth::id())->exists();
        $this->customer = Customer::first();
    }

    public function render()
    {
        if (!$this->customer) {
            return view('supplier.error');
        }
        $this->purchases = Auth::user()->purchases;

        return view('livewire.supplier-register-xml');
    }

    public function updatedXmlFile()
    {
        $this->prepareFile();
    }
    public function seeDetail($purchaseId)
    {
        $this->purchaseDetail = null;

        $this->purchase = Purchase::find($purchaseId);

        if ($this->purchase) {
            $this->purchaseDetail = $this->purchase->details;
            $this->isFormOpen = true;
        }
    }
    public function delete($purchaseId)
    {
        try {
            $this->purchaseDetail = null;

            // Encontrar la compra por ID
            $this->purchase = Purchase::find($purchaseId);

            // Verificar si la compra existe y si el supplier_id pertenece al usuario autenticado
            if ($this->purchase && $this->purchase->supplier->user_id === Auth::id()) {

                // Eliminar detalles de la compra relacionados
                $this->purchase->details()->delete();

                // Verificar si el archivo XML existe y eliminarlo
                if($this->purchase->xml_file){
                    if (Storage::disk('public')->exists($this->purchase->xml_file)) {
                        Storage::disk('public')->delete($this->purchase->xml_file);
                    }
                }
                // Eliminar la compra
                $this->purchase->delete();
                $this->alert('success', 'Registro Eliminado.');
            } else {
                // Retornar un mensaje de error si el usuario no tiene permisos para eliminar la compra
                $this->alert('error', 'No tienes permiso para eliminar esta compra.');
            }
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function downloadXML($path)
    {
        return Storage::disk('public')->download($path);
    }
    public function closeForm()
    {
        $this->purchase = null;
        $this->purchaseDetail = null;
        $this->isFormOpen = false;
    }
    public function prepareFile()
    {
        try {
            $content = file_get_contents($this->xml_file->getRealPath());

            // Carga el XML desde el contenido del archivo
            $xmlObject = simplexml_load_string($content);
            $namespaces = $xmlObject->getNamespaces(true);
            $cacObject = $xmlObject->children($namespaces['cac']);
            $cbcObject = $xmlObject->children($namespaces['cbc']);



            // Acceder directamente al nodo 'cac:AccountingCustomerParty'
            $accountingCustomerParty = $cacObject->AccountingCustomerParty;
            $AccountingSupplierParty = $cacObject->AccountingSupplierParty;
            $InvoiceTypeCode = (string) $cbcObject->InvoiceTypeCode;
            if ($InvoiceTypeCode != '01') {
                throw new Exception("Por el momento solo se recibe los XMLs de Facturas");
            }

            // Acceder a 'cac:Party' dentro de 'cac:AccountingCustomerParty'
            $party = $accountingCustomerParty->Party;
            $partySupplier = $AccountingSupplierParty->Party;

            // Acceder a 'cac:PartyIdentification' dentro de 'cac:Party'
            $partyIdentification = $party->PartyIdentification;
            $PartyIdentificationSuppplier = $partySupplier->PartyIdentification;
            $PartyNameSupplier = $partySupplier->PartyLegalEntity;


            // Finalmente, obtener el valor del nodo 'cbc:ID' dentro de 'cac:PartyIdentification'
            $ruc = $partyIdentification->children($namespaces['cbc'])->ID;
            $rucSupplier = (string)$PartyIdentificationSuppplier->children($namespaces['cbc'])->ID;
            $addressSuplierParty = $PartyNameSupplier->children($namespaces['cac'])->RegistrationAddress; //<cac:AddressLine> // <cbc:Line> 
            $addressSuplier = $addressSuplierParty->children($namespaces['cac'])->AddressLine;


            $this->name = (string) $PartyNameSupplier->children($namespaces['cbc'])->RegistrationName;
            $this->address = (string) $addressSuplier->children($namespaces['cbc'])->Line;
            $this->ruc = $rucSupplier;

            $myCompany = Company::where('ruc', $rucSupplier)->first();

            // Muestra el RUC extraído
            if ($ruc != $this->customer->ruc) {
                return $this->alert(
                    'error',
                    'El XML no pertenece a esta tienda, asegurese de que el XML pertenezca a la empresa ' . $this->customer->name . ' con RUC N° ' . $this->customer->ruc,
                    [
                        'timer' => 0, // Duración del alert en milisegundos (10 segundos)
                        'position' => 'center' // Puedes personalizar la posición si lo necesitas
                    ]
                );
            }
            if (!$myCompany) {
                return $this->alert('question', 'La empresa con RUC ' . $rucSupplier . ' no está registrada. ¿Deseas registrarla?', [
                    'showConfirmButton' => true,
                    'showCancelButton' => true,
                    'confirmButtonText' => 'Sí, registrar',
                    'cancelButtonText' => 'No, cancelar',
                    'onConfirmed' => 'registerCompany', // Evento que será disparado al confirmar
                    'onDismissed' => 'cancelled', // Evento que será disparado al cancelar
                    'position' => 'center',
                    'toast' => false,
                    'timer' => null,
                    'confirmButtonColor' => '#F5922A',
                    'cancelButtonColor' => '#2C2C2C',
                ]);
            }



            if ($myCompany) {

                $supplier_id = $myCompany->id;
                $invoiceCode = (string) $cbcObject->ID;

                $purchaseExists = Purchase::where('supplier_id', $supplier_id)->where('invoice_code', $invoiceCode)->exists();
                if ($purchaseExists) {
                    throw new Exception("Ya existe una compra con este codigo: " . $invoiceCode . ' para la compania: ' . $myCompany->name);
                }

                $purchaseDate = (string) $cbcObject->IssueDate; //2024-10-24
                $purchaseTime = (string) $cbcObject->IssueTime; //15:20:01.0Z

                $totalAmount = (float) $cacObject->LegalMonetaryTotal->children($namespaces['cbc'])->PayableAmount;
                $invoiceLines = $cacObject->InvoiceLine;
                $tax_amount = (float) $cacObject->TaxTotal->children($namespaces['cbc'])->TaxAmount;
                $sub_total = (float) $cacObject->LegalMonetaryTotal->children($namespaces['cbc'])->LineExtensionAmount;
                //$price = (float) $InvoiceLine->Price->children($namespaces['cbc'])->PriceAmount;

                $filename = Str::random(15) . '.xml';

                // Crear la ruta basada en el año y mes actual
                $subdirectory = Carbon::now()->format('Y/m');

                // Almacenar el archivo en la ubicación 'public/supplier_xml/{year}/{month}/{filename}'
                $xmlFilePath = $this->xml_file->storeAs("supplier_xml/{$subdirectory}", $filename, 'public');


                $detailData = [];

                foreach ($invoiceLines as $line) {
                    $detailData[] = [
                        'quantity' => (float)$line->children($namespaces['cbc'])->InvoicedQuantity,
                        'unit_price' => (float)$line->Price->children($namespaces['cbc'])->PriceAmount,
                        'total_price' => (float)$line->children($namespaces['cbc'])->LineExtensionAmount,
                        'product_price' => (float)$line->PricingReference->AlternativeConditionPrice->children($namespaces['cbc'])->PriceAmount,
                        'product_name' => (string)$line->Item->children($namespaces['cbc'])->Description,
                        'product_identification' => (string)$line->Item->SellersItemIdentification->children($namespaces['cbc'])->ID,
                        'product_tax_amount' => (float)$line->TaxTotal->children($namespaces['cbc'])->TaxAmount
                    ];
                }

                DB::transaction(function () use ($invoiceCode, $purchaseDate, $totalAmount, $detailData, $xmlFilePath, $supplier_id, $tax_amount, $sub_total) {
                    // Crear el registro de la compra
                    $purchase = Purchase::create([
                        'supplier_id' => $supplier_id, // Asignar el ID del proveedor (podrías obtenerlo dinámicamente)
                        'purchase_date' => $purchaseDate,
                        'invoice_code' => $invoiceCode,
                        'total_amount' => $totalAmount,
                        'status' => 'pending', // O el status que desees
                        'xml_file' => $xmlFilePath, // Guardar la ruta del archivo XML
                        'tax_amount' => $tax_amount,
                        'sub_total' => $sub_total
                    ]);

                    // Crear los detalles de la compra
                    foreach ($detailData as $productData) {
                        $productName = $productData['product_name'];
                        $productExists = Product::where('name', $productName)->first();
                        $productId = null;

                        if ($productExists) {
                            $productId = $productExists->id;
                        } else {
                            $product = Product::create(
                                [
                                    'code' => Str::random(15),
                                    'name' => $productData['product_name'],
                                    'purchase_price' => $productData['product_price'],
                                    'base_price' => $productData['unit_price'],
                                    'unit_type' => 1,
                                    'units_per_package' => 1,
                                    'is_active' => '1'
                                ]
                            );
                            $productId = $product->id;
                        }
                        if (!$productId) {
                            continue;
                        }

                        PurchaseDetail::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $productId, // Aquí deberías vincular el ID del producto
                            'quantity' => $productData['quantity'],
                            'price' => $productData['unit_price'],
                            'total_price' => $productData['total_price'],
                            'product_price' => $productData['product_price'],
                            'product_name' => $productData['product_name'],
                            'product_identification' => $productData['product_identification'],
                            'product_tax_amount' => $productData['product_tax_amount']
                        ]);
                    }
                });

                $this->alert('success', 'XML Cargado con éxito.');
            }
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function registerCompany()
    {
        $userId = Auth::id(); // Obtiene el ID del usuario autenticado

        $data = [
            'name' => $this->name,
            'ruc' => $this->ruc,
            'address' => $this->address,
            'user_id' => $userId, // Establece el ID del usuario autenticado
        ];

        Company::create($data);
        $this->alert('success', 'Compañía creada con éxito.');
        $this->prepareFile();
    }
}
