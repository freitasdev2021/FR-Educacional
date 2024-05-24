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
            @if(isset($Registro->id))
            <input type="hidden" value="{{$Registro->id}}" name="id">
            @endif
            <div class="col-sm-12 p-2">
               {{-- <pre>
                  {{print_r($EscolasRegistradas)}}
               </pre> --}}
                <div class="row">
                   <div class="col-sm-12">
                      <label>Descrição do Evento</label>
                      <textarea class="form-control" maxlength="250" name="DSEvento" placeholder="Max 250 Caracteres" rows="3" required>{{isset($Registro) ? $Registro->DSEvento : ''}}</textarea>
                   </div>
                </div>
                <br>
                @if(isset($Registro->id))
                  <div class="checkboxEscolas">
                     <div class="form-check escola">
                           {{-- <input type="hidden" name="Escola[]" value="{{isset($Registro->Escolas) && in_array($e->Nome,json_decode($Registro->Escolas,true)) ? $e->id : ''}}"> --}}
                           <input class="form-check-input" type="checkbox" value="1" name="alteraEvento" id="flexCheckDefault">
                           <label class="form-check-label" for="flexCheckDefault">
                           Modificar Participantes
                           </label>
                     </div>
                  </div>
                @endif
                <br>
                <label>Escolas Participantes</label>
                {{-- <pre>
                  {{print_r($EscolasRegistradas)}}
                </pre> --}}
                <div class="row">
                   <div class="checkboxTurnos">
                      @foreach($EscolasRegistradas as $key => $e)
                      <div class="form-check turno">
                         {{-- <input type="hidden" name="Escola[]" value="{{isset($Registro->Escolas) && in_array($e->Nome,json_decode($Registro->Escolas,true)) ? $e->id : ''}}"> --}}
                         <input class="form-check-input" type="checkbox" value="{{$e['IDEscola']}}" name="Escola[]"  {{(isset($e['Participando']) && $e['Participando'] == 1) ? 'checked' : ''}} id="flexCheckDefault">
                         <label class="form-check-label" for="flexCheckDefault">
                         {{$e['Escola']}}
                         |
                         <b>De</b>
                         <input type="datetime-local" name="DTInicio[]" value="{{isset($e['Participando']) && $e['Participando'] == 1 ? $e['INITurno'] : ''}}">
                         <b>Até</b>
                         <input  type="datetime-local" name="DTTermino[]" value="{{isset($e['Participando']) && $e['Participando'] == 1 ? $e['TERTurno'] : ''}}">
                         </label>
                      </div>
                      @endforeach
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