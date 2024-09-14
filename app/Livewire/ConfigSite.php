<?php

namespace App\Livewire;

use App\Models\SiteConfig;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Database\QueryException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Storage;

class ConfigSite extends Component
{
    use WithFileUploads;
    use LivewireAlert;

    public $site_name;
    public $site_language;
    public $site_description;
    public $site_config;
    public $site_logo;
    public $site_logo_url;
    public $site_logo_contrast;
    public $site_logo_contrast_url;
    public $site_logo_horizontal;
    public $site_logo_horizontal_url;
    public $site_favicon;
    public $site_favicon_url;
    public function mount()
    {
        $this->site_config = SiteConfig::first();

        if ($this->site_config) {
            $this->site_name = $this->site_config->site_name;
            $this->site_logo_url = $this->site_config->site_logo;
            $this->site_favicon_url = $this->site_config->site_favicon;
            $this->site_logo_horizontal_url = $this->site_config->site_logo_horizontal;
            $this->site_logo_contrast_url = $this->site_config->site_logo_contrast;
            $this->site_language = $this->site_config->site_language;
            $this->site_description = $this->site_config->site_description;
        }
    }

    public function store()
    {
        /*
        $this->validate([
            'site_name' => 'required|string|max:255',
            'site_favicon' => 'nullable|file|image|mimes:png,jpg,jpeg,svg|max:1024',
            'site_language' => 'required|string|max:10',
            'site_description' => 'nullable|string|max:500',
        ]);*/

        // Si se carga un nuevo favicon
        if ($this->site_favicon) {
            $this->site_favicon_url = $this->site_favicon->store('favicons', 'public');
        }
        if ($this->site_logo) {
            $this->site_logo_url = $this->site_logo->store('logos', 'public');
        }
        if ($this->site_logo_horizontal) {
            $this->site_logo_horizontal_url = $this->site_logo_horizontal->store('logo_horizontal', 'public');
        }
        if ($this->site_logo_contrast) {
            $this->site_logo_contrast_url = $this->site_logo_contrast->store('logo_contrast', 'public');
        }


        try {
            SiteConfig::updateOrCreate(
                ['id' => $this->site_config->id ?? null],
                [
                    'site_name' => $this->site_name,
                    'site_favicon' => $this->site_favicon_url,
                    'site_logo' => $this->site_logo_url,
                    'site_logo_contrast'=>$this->site_logo_contrast_url,
                    'site_logo_horizontal' => $this->site_logo_horizontal_url,
                    'site_language' => $this->site_language??'es',
                    'site_description' => $this->site_description,
                ]
            );

            $this->alert('success', 'Configuración del sitio actualizada correctamente.');
        } catch (QueryException $e) {
            $this->alert('error', 'Ocurrió un error al guardar la configuración. Intente nuevamente: ' . $e->getMessage());
        }
    }

    public function deleteFavicon()
    {
        $this->site_favicon = null;

        if ($this->site_favicon_url) {
            if (Storage::disk('public')->exists($this->site_favicon_url)) {
                Storage::disk('public')->delete($this->site_favicon_url);
            }

            $this->site_favicon_url = null;
            $this->site_config->site_favicon = null;
            $this->site_config->save();
        }
    }
    public function deleteLogo()
    {
        $this->site_logo = null;

        if ($this->site_logo_url) {
            if (Storage::disk('public')->exists($this->site_logo_url)) {
                Storage::disk('public')->delete($this->site_logo_url);
            }

            $this->site_logo_url = null;
            $this->site_config->site_logo = null;
            $this->site_config->save();
        }
    }
    public function deleteHorizontalLogo()
    {
        $this->site_logo_horizontal = null;

        if ($this->site_logo_horizontal_url) {
            if (Storage::disk('public')->exists($this->site_logo_horizontal_url)) {
                Storage::disk('public')->delete($this->site_logo_horizontal_url);
            }

            $this->site_logo_horizontal_url = null;
            $this->site_config->site_logo_horizontal = null;
            $this->site_config->save();
        }
    }
    public function deleteContrastLogo()
    {
        $this->site_logo_contrast = null;

        if ($this->site_logo_contrast_url) {
            if (Storage::disk('public')->exists($this->site_logo_contrast_url)) {
                Storage::disk('public')->delete($this->site_logo_contrast_url);
            }

            $this->site_logo_contrast_url = null;
            $this->site_config->site_logo_contrast = null;
            $this->site_config->save();
        }
    }
    
    public function render()
    {
        return view('livewire.config-site');
    }
   
}
