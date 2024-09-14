<?php

namespace App\Livewire;

use App\Models\Company;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Database\QueryException;
use Storage;
use Str;

class MyCompany extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    public $name;
    public $ruc;
    public $address;
    public $logo;
    public $sol_user;
    public $sol_pass;
    public $cert_path;
    public $cert_path_url;
    public $client_secret;
    public $production;
    public $company;
    public function mount(){
        $this->company = Company::first();
        if($this->company){
            $this->name = $this->company->name;
            $this->ruc = $this->company->ruc;
            $this->address = $this->company->address;
            $this->logo = $this->company->logo;
            $this->sol_user = $this->company->sol_user;
            $this->sol_pass = $this->company->sol_pass;
            $this->cert_path_url = $this->company->cert_path;
            $this->client_secret = $this->company->client_secret;
            $this->production = $this->company->production;
        }
    }
    public function store(){
        $this->validate([
            'name' => 'required|string|max:255',
            'ruc' => 'required|string|max:11',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'sol_user' => 'nullable|string|max:255',
            'sol_pass' => 'nullable|string|max:255',
            'cert_path' => 'nullable|file',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'required|boolean',
        ]);

        // Si se carga un nuevo certificado
        if ($this->cert_path) {
            // Generar un nombre aleatorio de 20 caracteres con extensión .pem
            $filename = Str::random(20) . '.pem';
    
            // Almacenar el archivo con el nombre generado
            $this->cert_path_url = $this->cert_path->storeAs('certificates', $filename, 'public');
        }
        // Intentar guardar o actualizar la empresa
        try {

            $this->company->name = $this->name;
            $this->company->ruc = $this->ruc;
            $this->company->address = $this->address;
            $this->company->logo = $this->logo;
            $this->company->sol_user = $this->sol_user;
            $this->company->sol_pass = $this->sol_pass;
            $this->company->cert_path = $this->cert_path_url;
            $this->company->client_secret = $this->client_secret;
            $this->company->production = $this->production;

            $this->company->save();

            $this->cert_path = null;

            $this->alert('success', 'Datos de la empresa actualizados correctamente.');
        } catch (QueryException $e) {
            $this->alert('error', 'Ocurrió un error al guardar los datos de la empresa. Por favor, intente nuevamente.');
        }
    }
    public function render()
    {
        return view('livewire.my-company');
    }
    public function deleteCert(){

        $this->cert_path = null;

        if ($this->cert_path_url) {
            if (Storage::disk('public')->exists($this->cert_path_url)) {
                Storage::disk('public')->delete($this->cert_path_url);
            }
    
            $this->cert_path_url = null;
        }
    }
}
