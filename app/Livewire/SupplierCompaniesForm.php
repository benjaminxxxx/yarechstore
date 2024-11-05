<?php

namespace App\Livewire;

use App\Models\Supplier as Company;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SupplierCompaniesForm extends Component
{
    //<!--DESARROLLO SUPPLIERCOMPANY-->
    use LivewireAlert;
    public $isFormOopen = false;
    public $name;
    public $ruc;
    public $contact_person;
    public $phone;
    public $email;
    public $address;
    public $whatsapp;
    public $companyId;
    protected $listeners = ['registerCompany','editCompany'=>'edit'];
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'ruc' => 'required|string|digits:11',
            'phone' => 'required|string|max:15',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:15',
        ];
    }

    // Mensajes de validación en español
    protected function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'ruc.required' => 'El campo RUC es obligatorio.',
            'ruc.digits' => 'El campo RUC debe tener 11 digitos.',
            'phone.required' => 'El campo teléfono es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'ruc.string' => 'El RUC debe ser una cadena de texto.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe ser un correo válido.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
        ];
    }
    public function render()
    {
        return view('livewire.supplier-companies-form');
    }
    public function edit($id){
        $company = Company::where('id',$id)->where('user_id',Auth::id())->first();
        if($company){
            $this->companyId = $company->id;
            $this->name = $company->name;
            $this->ruc = $company->ruc;
            $this->contact_person = $company->contact_person;
            $this->phone =$company->phone;
            $this->email = $company->email;
            $this->address = $company->address;
            $this->whatsapp = $company->whatsapp;
            $this->isFormOopen = true;
        }else{
            $this->alert('error', "Usuario Inválido");
        }

    }
    public function save()
    {

        $this->validate(); // Ejecuta la validación

        try {
            $userId = Auth::id(); // Obtiene el ID del usuario autenticado

            $data = [
                'name' => $this->name,
                'ruc' => $this->ruc,
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'whatsapp' => $this->whatsapp,
                'user_id' => $userId, // Establece el ID del usuario autenticado
            ];

            // Verifica si se está actualizando o creando una nueva compañía
            if ($this->companyId) {
                Company::where('id', $this->companyId)->update($data);
                $this->alert('success', 'Compañía actualizada con éxito.');
            } else {
                Company::create($data);
                $this->alert('success', 'Compañía creada con éxito.');
            }
            $this->dispatch('refreshList');
            $this->closeForm(); // Reinicia el formulario
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function registerCompany()
    {
        $this->resetForm();
        $this->isFormOopen = true;
    }
    public function closeForm()
    {
        $this->resetForm();
        $this->isFormOopen = false;
    }
    public function resetForm()
    {
        $this->name = null;
        $this->ruc = null;
        $this->contact_person = null;
        $this->phone = null;
        $this->email = null;
        $this->address = null;
        $this->whatsapp = null;
        $this->resetErrorBag();
    }
}
