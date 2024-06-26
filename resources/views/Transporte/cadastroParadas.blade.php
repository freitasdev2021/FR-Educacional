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
                <form action="{{route('Transporte/Paradas/Save')}}" method="POST">
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
                    @if(isset($Registro->IDParada))
                    <input type="hidden" name="id" value="{{$Registro->IDParada}}">
                    @endif
                    <input type="hidden" name="IDRota" value="{{$IDRota}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nome</label>
                            <input type="text" name="Nome" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Registro->Nome : ''}}">
                        </div>
                        <div class="col-sm-6">
                            <label>Hora</label>
                            <input type="time" name="Hora" class="form-control" value="{{isset($Registro) ? $Registro->Hora : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Transporte/Paradas',$IDRota)}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>