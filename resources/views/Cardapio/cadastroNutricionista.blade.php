<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Merenda/Nutricionistas/Save')}}" method="POST">
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
                   <div class="col-sm-6">
                     <label>Nome</label>
                     <input type="text" class="form-control" name="Nome" value="{{isset($Registro) ? $Registro->Nome : ''}}" required>
                   </div>
                   <div class="col-sm-6">
                    <label>Contrato</label>
                    <input type="date" class="form-control" name="Contrato" value="{{isset($Registro) ? $Registro->Contrato : ''}}" required>
                  </div>
               </div>
               <div class="row">
                <div class="col-sm-6">
                    <label>CRN/CFN</label>
                    <input type="text" class="form-control" name="CRN" value="{{isset($Registro) ? $Registro->CRN : ''}}" required>
                  </div>
                  <div class="col-sm-6">
                    <label>Email</label>
                    <input type="text" class="form-control" name="Email" value="{{isset($Registro) ? $Registro->Email : ''}}" required>
                  </div>
               </div>
               <div class="row">
                    <div class="col-sm-6">
                        <label>Celular</label>
                        <input type="text" class="form-control" name="Celular" value="{{isset($Registro) ? $Registro->Celular : ''}}" required>
                    </div>
                    <div class="col-sm-6">
                        <label>Tipo de Contrato</label>
                        <select name="TPContrato" class="form-control">
                            <option value="">Selecione</option>
                            <option value="Efetivo" {{isset($Registro) && $Registro->TPContrato == "Efetivo" ? 'selected' : ''}}>Efetivo</option>
                            <option value="Temporario" {{isset($Registro) && $Registro->TPContrato == "Temporario" ? 'selected' : ''}}>Temporario</option>
                        </select>
                    </div>
                </div>
                @if(isset($id))
                <br>
                <div class="checkboxEscolas">
                    <div class="form-check escola">
                        <input class="form-check-input" type="checkbox" value="1" name="credenciais" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                         Enviar Novas Credenciais de Login
                        </label>
                    </div>
                </div>
                @endif
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Merenda/Nutricionistas/index')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
       </div>
    </div>
 </x-educacional-layout>