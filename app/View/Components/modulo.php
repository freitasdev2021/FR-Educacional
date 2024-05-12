<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class modulo extends Component
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
        $modulo = $partes[0];

        $active = "";
        if($modulo == $this->endereco){
            $active = "active";
        }

        return view('components.modulo',[
            "active" => $active
        ]);
    }
}
