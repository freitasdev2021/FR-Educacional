<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <form action="{{route('Calendario/Paralizacoes/Save')}}" method="POST">
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
             @if(isset($Registro->IDParalizacao))
             <input type="hidden" value="{{$Registro->IDParalizacao}}" name="id">
             @endif
             <div class="col-sm-12 p-2">
                <div class="col-sm-12">
                   <label>Escola</label>
                   <select class="form-control" name="IDEscola" required>
                      <option>Selecione</option>
                      @foreach($Escolas as $es)
                        <option value="{{$es->id}}" {{(isset($Registro->IDEscola) && $Registro->IDEscola == $es->id) ? 'selected' : ''}}>{{$es->Nome}}</option>
                      @endforeach
                   </select>
                </div>
                <div class="col-sm-12">
                    <label>Descrição do Motivo</label>
                    <textarea class="form-control" maxlength="250" name="DSMotivo" required>{{isset($Registro) ? $Registro->DSMotivo : ''}}</textarea>
                </div>
                <div class="col-sm-12 row">
                   <div class="col-sm-6">
                      <label>De</label>
                      <input type="date" class="form-control" name="DTInicio" value="{{isset($Registro->Inicio) ? $Registro->Inicio : ''}}" required>
                   </div>
                   <div class="col-sm-6">
                      <label>Até</label>
                      <input type="date" class="form-control" name="DTTermino" value="{{isset($Registro->Termino) ? $Registro->Termino : ''}}" required>
                   </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                      <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                      <a class="btn btn-light" href="{{route('Calendario/Paralizacoes')}}">Cancelar</a>
                   </div>
                </div>
             </div>
          </form>
       </div>
    </div>
 </x-educacional-layout>