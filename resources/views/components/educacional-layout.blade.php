@extends('layouts.app')

@section('content')
<body className='snippet-body'>
    <body classname="snippet-body" id="body-pd" class="body-pd" cz-shortcut-listen="true">
       <header class="header bg-fr" id="header">
                
       </header>
       <div class="l-navbar show" id="nav-bar">
          <nav class="nav" >
             <div>
                <a href="{{route('dashboard')}}" class="nav_logo"><i class='bx bx-book-reader text-white'></i><span class="nav_logo-name">FR Educacional</span> </a>
                <div class="nav_list"> 
                  @if(in_array(Auth::user()->tipo,[5,6]))
                  <x-modulo nome="Alunos" icon="bx bxs-group" rota="Alunos/index" endereco="Alunos"/>
                  <x-modulo nome="Calendário" icon="bx bx-calendar" rota="Calendario/index" endereco="Calendario"/>
                  <x-modulo nome="Turmas" icon="bx bxs-graduation" rota="Turmas/index" endereco="Turmas"/>
                  <x-modulo nome="Aulas" icon="bx bxs-book" rota="Aulas/index" endereco="Aulas"/>
                  <x-modulo nome="Planejamentos" icon="bx bx-list-ol" rota="Planejamentos/index" endereco="Planejamentos"/>
                  <x-modulo nome="Ocorrências" icon="bx bx-highlight" rota="Ocorrencias/index" endereco="Ocorrencias"/>
                  <x-modulo nome="Ficha Avaliativa" icon="bx bx-spreadsheet" rota="Fichas/index" endereco="Fichas"/>
                  <x-modulo nome="Responsáveis" icon="bx bx-body" rota="Responsaveis/index" endereco="Responsaveis"/>
                  @if(Auth::user()->tipo == 6)
                  <x-modulo nome="Apoio" icon="bx bxs-universal-access" rota="Apoio/index" endereco="Apoio"/>
                  @endif
                  @endif
                  @if(Auth::user()->tipo == 2)
                  <x-modulo nome="Diretores" icon="bx bxs-briefcase-alt" rota="Diretores/index" endereco="Diretores"/>
                  @endif
                  @if(Auth::user()->tipo == 0)
                  <x-modulo nome="Secretarías" icon="bx bx-buildings" rota="Secretarias/index" endereco="Secretarias"/>
                  <x-modulo nome="Usuários" icon="bx bx-user" rota="Usuarios/indexFornecedor" endereco="Usuarios"/>
                  @elseif(in_array(Auth::user()->tipo,[2,4]))
                  <x-modulo nome="Escola{{(Auth::user()->tipo == 2) ? 's' : ''}}" icon="bx bxs-school" rota="Escolas/index" endereco="Escolas"/>
                  <x-modulo nome="Professores" icon="bx bxs-book-reader" rota="Professores/index" endereco="Professores"/>
                  <x-modulo nome="Aulas" icon="bx bxs-book" rota="Aulas/index" endereco="Aulas"/>
                  <x-modulo nome="Pedagogos" icon="bx bx-library" rota="Pedagogos/index" endereco="Pedagogos"/>
                  <x-modulo nome="Ocorrências" icon="bx bx-highlight" rota="Ocorrencias/index" endereco="Ocorrencias"/>
                  <x-modulo nome="Ficha Avaliativa" icon="bx bx-spreadsheet" rota="Fichas/index" endereco="Fichas"/>
                  {{-- <x-modulo nome="Responsaveis" icon="bx bx-male-female" rota="Responsaveis/index" endereco="Responsaveis"/> --}}
                  <x-modulo nome="Alunos" icon="bx bxs-group" rota="Alunos/index" endereco="Alunos"/>
                  @if(Auth::user()->tipo == 4)<x-modulo nome="Auxiliares" icon="bx bxs-user-detail" rota="Auxiliares/index" endereco="Auxiliares"/>@endif
                  <x-modulo nome="Calendário" icon="bx bx-calendar" rota="Calendario/index" endereco="Calendario"/>
                  <x-modulo nome="Endereços" icon="bx bx-street-view" rota="Enderecos/index" endereco="Enderecos"/>
                  @if(in_array(Auth::user()->tipo,[3,4]))<x-modulo nome="Merenda" icon="bx bx-fork" rota="Merenda/index" endereco="Merenda"/>@endif
                  <x-modulo nome="Transporte" icon="bx bx-bus-school" rota="Transporte/index" endereco="Transporte"/>
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
       <div class="bari" style="margin-top:100px; margin-right:15px;">
          {{$slot}}
       </div>
       <!--Container Main end-->
       <script>
         windowHeight = $(window).height()
         $(".bari").css("height",windowHeight)
       </script>
 </body>
@endsection