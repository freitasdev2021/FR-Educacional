<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <form action="{{route('Calendario/Sabados/Save')}}" method="POST">
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
             @if(isset($Registro->IDSabado))
             <input type="hidden" value="{{$Registro->IDSabado}}" name="id">
             @endif
             <div class="col-sm-12 p-2">
                <div class="col-sm-12 row">
                  <div class="col-sm-6">
                    <label>Escola</label>
                    <select class="form-control" name="IDEscola" required>
                       <option>Selecione</option>
                       @foreach($Escolas as $es)
                       <option value="{{$es->id}}" {{(isset($Registro) && $Registro->IDEscola == $es->id) ? 'selected' : ''}}>{{$es->Nome}}</option>
                       @endforeach
                    </select>
                  </div>
                  <div class="col-sm-6">
                    <label>Sabado Letivo</label>
                    <select class="form-control" name="Data" required>
                       <option>Selecione</option>
                       @foreach($Sabados as $sa)
                       <option value="{{$sa}}" {{(isset($Registro->Sabado) && $Registro->Sabado == \App\Http\Controllers\Controller::data($sa,'Y-m-d')) ? 'selected' : ''}}>{{$sa}}</option>
                       @endforeach
                    </select>
                  </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                      <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                      <a class="btn btn-light" href="{{route('Calendario/Sabados')}}">Cancelar</a>
                   </div>
                </div>
             </div>
          </form>
       </div>
    </div>
 </x-educacional-layout>