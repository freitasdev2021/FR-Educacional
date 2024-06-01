<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],['id' => $id,'idrota' => $IDRota])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Transporte/Rodagem/Save')}}" method="POST">
                    @csrf
                    @method("POST")
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
                    <input type="hidden" name="IDRota" value="{{$IDRota}}">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Veículo</label>
                            <select name="IDVeiculo" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($Veiculos as $v)
                                <option value="{{$v['id']}}">{{$v['Marca']." ".$v['Nome']." - ".$v['Placa']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Quilomentragem Inicial</label>
                            <input type="text" name="KMInicial" class="form-control" maxlength="50" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Quilometragem Final</label>
                            <input type="text" name="KMFinal" class="form-control" required>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Transporte/Rodagem',$IDRota)}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>