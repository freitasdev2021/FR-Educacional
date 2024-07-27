<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Calendario/Eventos/Save')}}" method="POST">
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
            @if(isset($Registro->IDReuniao))
            <input type="hidden" value="{{$Registro->IDReuniao}}" name="id">
            @endif
            <div class="col-sm-12 p-2">
               <div class="row">
                  <div class="col-sm-6">
                     <label>Escola</label>
                     <select name="IDEscola" class="form-control">
                        <option value="">Selecione</option>
                        @foreach($Escolas as $e)
                        <option value="{{$e->id}}" {{isset($Registro) && $Registro->IDEscola == $e->id ? 'selected' : ''}}>{{$e->Nome}}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="col-sm-6">
                    <label>Turma</label>
                    <select name="IDEscola" class="form-control">
                       <option value="">Selecione</option>
                       
                    </select>
                 </div>
               </div>
                <div class="row">
                   <div class="col-sm-12">
                      <label>Descrição do Evento</label>
                      <textarea class="form-control" maxlength="250" name="DSEvento" placeholder="Max 250 Caracteres" rows="3" required>{{isset($Registro) ? $Registro->DSEvento : ''}}</textarea>
                   </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <label>Data</label>
                        <input type="date" name="Data" class="form-control" value="{{isset($Registro) ? $Registro->Data : ''}}">
                     </div>
                     <div class="col-sm-4">
                        <label>Inicio</label>
                        <input type="time" name="Inicio" class="form-control" value="{{isset($Registro) ? $Registro->Inicio : ''}}">
                     </div>
                     <div class="col-sm-4">
                        <label>Termino</label>
                        <input type="time" name="Termino" class="form-control" value="{{isset($Registro) ? $Registro->Termino : ''}}">
                     </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Calendario/Eventos')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
       </div>
    </div>
 </x-educacional-layout>