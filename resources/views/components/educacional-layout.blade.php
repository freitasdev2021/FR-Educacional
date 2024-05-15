@extends('layouts.app')

@section('content')
<body className='snippet-body'>
    <body id="body-pd">
       <header class="header" id="header">
          <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
          <div class="header_img"> <img src="https://i.imgur.com/hczKIze.jpg" alt=""> </div>
          
       </header>
       <div class="l-navbar" id="nav-bar">
          <nav class="nav" >
             <div>
                <a href="{{route('dashboard')}}" class="nav_logo"><i class='bx bx-book-reader text-white'></i><span class="nav_logo-name">FR Educacional</span> </a>
                <div class="nav_list"> 
                  @if(Auth::user()->tipo == 0)
                  <x-modulo nome="Secretarías" icon="bx bx-buildings" rota="Secretarias/index" endereco="Secretarias"/>
                  <x-modulo nome="Usuários" icon="bx bx-user" rota="Usuarios/indexFornecedor" endereco="Usuarios"/>
                  @elseif(Auth::user()->tipo == 2)
                  <x-modulo nome="Escolas" icon="bx bxs-school" rota="Escolas/index" endereco="Escolas"/>
                  <x-modulo nome="Coordenadores" icon="bx bxs-briefcase" rota="Coordenadores/index" endereco="Coordenadores"/>
                  <x-modulo nome="Diretores" icon="bx bxs-briefcase-alt" rota="Diretores/index" endereco="Diretores"/>
                  <x-modulo nome="Professores" icon="bx bxs-book-reader" rota="Professores/index" endereco="Professores"/>
                  <x-modulo nome="Pedagogos" icon="bx bx-library" rota="Pedagogos/index" endereco="Pedagogos"/>
                  <x-modulo nome="Responsaveis" icon="bx bx-male-female" rota="Responsaveis/index" endereco="Responsaveis"/>
                  <x-modulo nome="Alunos" icon="bx bxs-group" rota="Alunos/index" endereco="Alunos"/>
                  <x-modulo nome="Auxiliares" icon="bx bxs-user-detail" rota="Auxiliares/index" endereco="Auxiliares"/>
                  <x-modulo nome="Apoio" icon="bx bxs-universal-access" rota="Apoio/index" endereco="Apoio"/>
                  <x-modulo nome="Calendário" icon="bx bx-calendar" rota="Calendario/index" endereco="Calendario"/>
                  <x-modulo nome="Acompanhamento" icon="bx bx-calendar-check" rota="Acompanhamento/index" endereco="Acompanhamento"/>
                  <x-modulo nome="Cardápio" icon="bx bx-fork" rota="Cardapio/index" endereco="Cardapio"/>
                  @endif 
                </div>
             </div>
             <form action="{{route('logout')}}" method="POST">
               @csrf
               <button class="nav_link sair" type="submit"><i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Sair</span> </button>
             </form>
          </nav>
       </div>
       <!--Container Main start-->
       <div class="height-100" style="margin-top:100px; margin-right:15px;">
          {{$slot}}
       </div>
       <!--Container Main end-->
 </body>
 </body>
@endsection