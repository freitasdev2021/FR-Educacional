<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <!--CABECALHO-->
          <div class="col-sm-12 p-2 row">
             <div class="col-auto">
                <div class="input-group mb-3">
                   <select class="form-control">
                      <option>Selecione</option>
                      @foreach($Escolas as $es)
                      <option value="{{$es->id}}">{{$es->Nome}}</option>
                      @endforeach
                   </select>
                   <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class='bx bxs-filter-alt' ></i></button>
                </div>
             </div>
          </div>
          <!--LISTAS-->
          <link rel="stylesheet" href="{{asset('plugins/calendar/css/style.css')}}">
          <div class="col-sm-12 p-2 ">
             <hr>
             <div class="d-flex justify-content-center">
                <h1>Teste</h1>
             </div>
          </div>
          <!--//-->
       </div>
    </div>
 </x-educacional-layout>