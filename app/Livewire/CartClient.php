<?php

namespace App\Livewire;

use Livewire\Component;
use GuzzleHttp\Client;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\DocumentSunatType;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;


class CartClient extends Component
{
    public $saleCode;
    public $isFormOpen = false;
    public $searchCustomer;
    public $resultsCustomer = [];
    public $customerId;
    public $document_type = '0';      // Tipo de Documento
    public $document_number;    // Número de Documento
    public $name;               // Nombre/Razón Social
    public $address;            // Dirección
    public $department;         // Departamento
    public $province;           // Provincia
    public $district;           // Distrito
    public $phone;              // Teléfonos
    public $commercial_name;    // Nombre Comercial
    public $email;
    public $sale;
    public $customers;
    public $documentTypes;
    protected $listeners = ['saleUpdated' => '$refresh'];
    public function searchingCustomers()
    {
        if (!empty($this->searchCustomer)) {
            $this->resultsCustomer = Customer::where(function($query) {
                $search = '%' . $this->searchCustomer . '%';
    
                $query->where('fullname', 'like', $search)
                      ->orWhere('document_number', 'like', $search)
                      ->orWhere('address', 'like', $search)
                      ->orWhere('district', 'like', $search)
                      ->orWhere('department', 'like', $search)
                      ->orWhere('commercial_name', 'like', $search);
            })->get();

        } else {
            // Si la búsqueda está vacía, puedes optar por vaciar los resultados o hacer algo diferente
            $this->resultsCustomer = collect();
        }
    }
    public function select($costumerId){
        $this->selectClient($costumerId);
        $this->closeForm();
    }
    public function selectClient($costumerId){
        if($this->sale){
            $customer = Customer::find($costumerId);
            if($customer){
                $this->sale->customer_id = $customer->id;
                $this->sale->customer_name = $customer->fullname;
                $this->sale->customer_document_type = $customer->document_type_id;
                $this->sale->customer_document = $customer->document_number;
                $this->sale->save();
            }
        }
    }
    public function sunat()
    {
        $token = "abcxyz";
        $ruc = $this->document_number;

        $client = new Client();

        $url = "http://localhost/apisunat/public/api/v1/ruc/{$ruc}?token={$token}";

        try {
            $response = $client->request('GET', $url);

            // Obtener el cuerpo de la respuesta
            $body = $response->getBody();

            // Decodificar el JSON
            $data = json_decode($body, true);

            $this->document_number = $data['ruc'] ?? null;
            $this->name = $data['razonSocial'] ?? null;
            $this->commercial_name = $data['nombreComercial'] ?? null;
            $this->address = $data['direccion'] ?? null;
            $this->department = $data['departamento'] ?? null;
            $this->province = $data['provincia'] ?? null;
            $this->district = $data['distrito'] ?? null;

            // Para los teléfonos, si es necesario convertirlos a una cadena o lista
            $this->phone = implode(', ', $data['telefonos']) ?? null;

            return session()->flash('message', 'Datos obtenidos con exito');
            /*
            {"ruc":"10776858507","razonSocial":"QUISPE RAMOS BENJAMIN ELEODORO","nombreComercial":"-\r\n\t\t\t\t\t              \r\n\t\t\t\t\t              \t\r\n\t\t\t\t\t                Afecto al Nuevo RUS: SI","telefonos":[],"tipo":"PERSONA NATURAL CON NEGOCIO","estado":"ACTIVO","condicion":"HABIDO","direccion":"-","departamento":null,"provincia":null,"distrito":null,"fechaInscripcion":"2015-02-27T00:00:00.000Z","sistEmsion":"MANUAL","sistContabilidad":"MANUAL","actExterior":"SIN ACTIVIDAD","actEconomicas":["Principal    - 4789 - VENTA AL POR MENOR DE OTROS PRODUCTOS EN PUESTOS DE VENTA Y MERCADOS","Secundaria 1 - 9609  - OTRAS ACTIVIDADES DE SERVICIOS PERSONALES N.C.P."],"cpPago":["BOLETA DE VENTA"],"sistElectronica":["RECIBOS POR HONORARIOS     AFILIADO DESDE 28\/02\/2015"],"fechaEmisorFe":"2015-02-28T00:00:00.000Z","cpeElectronico":["RECIBO POR HONORARIO (desde 28\/02\/2015)"],"fechaPle":null,"padrones":["NINGUNO"],"fechaBaja":null,"profesion":""}
            */
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Manejar errores de solicitud aquí
            return session()->flash('error', 'Error en la solicitud');
        }
    }
    public function store()
    {
        $validatedData = $this->validate([
            'document_type' => 'required|string|max:255',
            'document_number' => [
            'required',
            'string',
            'max:20',
            Rule::unique('customers', 'document_number')->ignore($this->customerId),
        ],
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'commercial_name' => 'nullable|string|max:255',
        ]);

        try {
            if ($this->customerId) {
                $customer = Customer::find($this->customerId);
                if ($customer) {
                    $customer->update([
                        'document_type_id' => $this->document_type,
                        'document_number' => $this->document_number,
                        'fullname' => $this->name,
                        'address' => $this->address,
                        'department' => $this->department,
                        'province' => $this->province,
                        'district' => $this->district,
                        'phone' => $this->phone,
                        'customer_type_id' => strlen($this->document_number) == 8 ? 1 : 2,
                        'commercial_name' => $this->commercial_name,
                    ]);
        
                    session()->flash('message', 'Cliente actualizado exitosamente.');
                } else {
                    session()->flash('error', 'El cliente ya no existe.');
                }
            }else{
                $customer = Customer::create([
                    'document_type_id' => $this->document_type,
                    'document_number' => $this->document_number,
                    'fullname' => $this->name,
                    'address' => $this->address,
                    'department' => $this->department,
                    'province' => $this->province,
                    'district' => $this->district,
                    'phone' => $this->phone,
                    'customer_type_id'=> strlen($this->document_number)==8?1:2,
                    'commercial_name' => $this->commercial_name,
                ]);

                session()->flash('message', 'Datos registrados con éxito');
            }
            

            if($this->sale && $customer){
                $this->selectClient($customer->id);
                $this->isFormOpen = false;
            }

            // Establecer mensaje de éxito
            
            // Limpiar las propiedades después de la operación (opcional)
            $this->resetForm();
        } catch (QueryException $e) {
            // Manejar el error y establecer mensaje de error
            session()->flash('error', 'Error en la solicitud: ' . $e->getMessage());
        }
    }
    public function createNewCostumer(){
        $this->isFormOpen = true;
    }
    public function closeForm(){
        if ($this->customerId) {
            $this->resetForm(); // Solo resetea el formulario, pero no lo cierra si se está editando
        } else {
            $this->resetForm(); // Resetea el formulario
            $this->isFormOpen = false; // Cierra el formulario si no se está editando
        }
    }
    public function resetForm()
    {
        $this->customerId = null;
        $this->document_type = '';
        $this->document_number = '';
        $this->name = '';
        $this->address = '';
        $this->department = '';
        $this->province = '';
        $this->district = '';
        $this->phone = '';
        $this->commercial_name = '';
    }
    public function deleteCostumer(){
        if($this->sale){
            $this->sale->customer_id = null;
            $this->sale->customer_name = null;
            $this->sale->customer_document_type = null;
            $this->sale->customer_document = null;
            $this->sale->save();
        }
    }
    public function delete($customerId){
        $customer = Customer::find($customerId);
        if($customer){
            $customer->delete();
        }
    }
    public function edit($customerId){
        $customer = Customer::find($customerId);
        if($customer){
            $this->customerId = $customer->id;
            $this->document_type = $customer->document_type_id;
            $this->document_number = $customer->document_number;
            $this->name = $customer->fullname;
            $this->address = $customer->address;
            $this->department = $customer->department;
            $this->province = $customer->province;
            $this->district = $customer->district;
            $this->phone = $customer->phone;
            $this->commercial_name = $customer->commercial_name;
        }else{
            session()->flash('error', 'El cliente ya no existe');
        }
    }
    public function mount(){
        $this->sale = Sale::where('code',$this->saleCode)->first();
        $this->documentTypes = DocumentSunatType::all();
    }
    public function render()
    {
        $this->customers = Customer::all();
        return view('livewire.cart-client');
    }
}
