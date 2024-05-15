<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class submodulo extends Component
{
    public $rota;
    public $nome;

    public $icon;
    public $endereco;
    /**
     * Create a new component instance.
     */
    public function __construct($rota,$nome,$icon,$endereco)
    {
        $this->rota = $rota;
        $this->nome = $nome;
        $this->icon = $icon;
        $this->endereco = $endereco;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        $original_string = Route::currentRouteName();
        $partes = explode('/', $original_string);
        $modulo = isset($partes[1]) ? $partes[1] : '';

        $active = "";
        if($modulo == $this->endereco){
            $active = "active-submodulo";
        }

        return view('components.submodulo',[
            "active" => $active
        ]);
    }
}
