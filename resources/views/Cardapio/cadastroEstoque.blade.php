<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <form action="{{route('Merenda/Estoque/Save')}}" method="POST">
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
            @if(isset($Registro->IDEstoque))
            <input type="hidden" value="{{$Registro->IDEstoque}}" name="id">
            @endif
            <div class="col-sm-12 p-2">
               {{-- <pre>
                  {{print_r($EscolasRegistradas)}}
               </pre> --}}
                <div class="row">
                    <div class="col-sm-6">
                        <label>Nome do Item</label>
                        <input type="text" name="Item" class="form-control" value="{{isset($Registro) ? $Registro->Item : ''}}">
                    </div>
                    <div class="col-sm-6">
                        <label>Quantidade</label>
                        <input type="text" name="Quantidade" class="form-control" {{(Route::currentRouteName() == 'Merenda/Estoque/Edit') ? 'disabled' : ''}} value="{{isset($Registro) ? $Registro->Quantidade : ''}}">
                    </div>
                </div>
                <div class="row">
                   <div class="col-sm-6">
                     <label>Vencimento</label>
                     <input type="date" class="form-control" name="Vencimento" value="{{isset($Registro) ? $Registro->Vencimento : ''}}">
                   </div>
                   <div class="col-sm-6">
                    <label>Tipo UN</label>
                    <select class="form-control" name="TPUnidade">
                        <option>Selecione</option>
                        <option value="UN" {{isset($Registro) && $Registro->Unidade == 'UN' ? 'selected' : ''}}>UN</option>
                        <option value="KG" {{isset($Registro) && $Registro->Unidade == 'KG' ? 'selected' : ''}}>KG</option>
                    </select>
                   </div>
                </div>
                <br>
                <div class="row">
                   <div class="col-auto">
                       <button class="btn btn-fr">Salvar</button>
                   </div>
                   <div class="col-auto">
                       <a class="btn btn-light" href="{{route('Merenda/Estoque/index')}}">Cancelar</a>
                   </div>
                </div>
             </div>
        </form>
       </div>
    </div>
 </x-educacional-layout>