<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Merenda/Save')}}" method="POST">
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
            @if(isset($Registro->IDMerenda))
            <input type="hidden" value="{{$Registro->IDMerenda}}" name="id">
            @endif
            <div class="col-sm-12 p-2">
               {{-- <pre>
                  {{print_r($EscolasRegistradas)}}
               </pre> --}}
               <div class="row">
                   <div class="col-sm-6">
                     <label>Data da Refeição</label>
                     <input type="date" class="form-control" name="Dia" value="{{isset($Registro) ? $Registro->Dia : ''}}">
                   </div>
                   <div class="col-sm-6">
                    <label>Turno</label>
                    <select class="form-control" name="Turno">
                        <option>Selecione</option>
                        <option value="Manhã" {{isset($Registro) && $Registro->Turno == 'Manhã' ? 'selected' : ''}}>Manhã</option>
                        <option value="Tarde" {{isset($Registro) && $Registro->Turno == 'Tarde' ? 'selected' : ''}}>Tarde</option>
                        <option value="Noite" {{isset($Registro) && $Registro->Turno == 'Noite' ? 'selected' : ''}}>Noite</option>
                    </select>
                   </div>
               </div>
                <div class="row">
                   <div class="col-sm-12">
                      <label>Descrição da Refeição</label>
                      <textarea class="form-control" maxlength="250" name="Descricao" placeholder="Max 250 Caracteres" rows="3" required>{{isset($Registro) ? $Registro->Descricao : ''}}</textarea>
                   </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Merenda/index')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
       </div>
    </div>
 </x-educacional-layout>