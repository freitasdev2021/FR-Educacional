<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Merenda/Contratos/Save')}}" method="POST">
            @csrf
            @if(session('success'))
            <div class="col-sm-12 shadow p-2 bg-success text-white">
                <strong>{{session('success')}}</strong>
            </div>
            @elseif(session('error'))
            <div class="col-sm-12 shadow p-2 bg-danger text-white">
                <strong>{{session('error')}}</strong>
            </div>
            <br>
            @endif
            @if(isset($Registro))
            <input type="hidden" value="{{$id}}" name="id">
            @endif
            <div class="col-sm-12 p-2">
               {{-- <pre>
                  {{print_r($EscolasRegistradas)}}
               </pre> --}}
               <div class="row">
                   <div class="col-sm-3">
                     <label>Empresa</label>
                     <input type="text" class="form-control" name="NMEmpresa" value="{{isset($Registro) ? $Registro->NMEmpresa : ''}}">
                   </div>
                   <div class="col-sm-3">
                    <label>Vigência</label>
                    <input type="date" class="form-control" name="Vigencia" value="{{isset($Registro) ? $Registro->Vigencia : ''}}">
                  </div>
                  <div class="col-sm-3">
                    <label>Valor do Contrato</label>
                    <input type="text" class="form-control" name="VLContrato" value="{{isset($Registro) ? $Registro->VLContrato : ''}}">
                  </div>
                  <div class="col-sm-3">
                    <label>N° do Processo</label>
                    <input type="text" class="form-control" name="NProcesso" value="{{isset($Registro) ? $Registro->NProcesso : ''}}">
                  </div>
               </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Merenda/Contratos/index')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
        @if(isset($id))
        <hr>
        <div class="row">
         <div class="col-sm-6">
            <h5 align="center">Autorizações de Fornecimento</h5>
            <form action="{{route('Merenda/AF/Save')}}" method="POST">
               @csrf
               <div class="row">
                  <input type="hidden" value="{{$id}}" name="IDContrato">
                  <div class="col-sm-6">
                     <label>Autorizacao</label>
                     <input type="text" name="Autorizacao" class="form-control">
                  </div>
                  <div class="col-sm-6">
                     <label>Local</label>
                     <input type="text" name="Local" class="form-control">
                  </div>
               </div>
               <br>
               @if($AF)
               <table class="table table-striped">
                  <thead>
                     <tr>
                        <th>Autorização</th>
                        <th>Local</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($AF as $e)
                     <tr>
                        <td>{{$e->Autorizacao}}</td>
                        <td>{{$e->Local}}</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @endif
               <hr>
               <div class="col-auto">
                  <button class="btn btn-fr">Autorizar</button>
               </div>
            </form>
         </div>
         <div class="col-sm-6">
            <h5 align="center">Empenhos</h5>
            <form action="{{route('Merenda/Empenho/Save')}}" method="POST">
               @csrf
               <div class="row">
                  <input type="hidden" value="{{$id}}" name="IDContrato">
                  <div class="col-sm-6">
                     <label>Empenho</label>
                     <input type="text" name="Ordem" class="form-control">
                  </div>
                  <div class="col-sm-6">
                     <label>Valor</label>
                     <input type="text" name="Valor" class="form-control">
                  </div>
               </div>
               <br>
               @if($Empenho)
               <table class="table table-striped">
                  <thead>
                     <tr>
                        <th>Autorização</th>
                        <th>Local</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($Empenho as $e)
                     <tr>
                        <td>{{$e->Ordem}}</td>
                        <td>{{$e->Valor}}</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @endif
               <hr>
               <div class="col-auto">
                  <button class="btn btn-fr">Empenhar</button>
               </div>
            </form>
         </div>
        </div>
        @endif
       </div>
    </div>
 </x-educacional-layout>