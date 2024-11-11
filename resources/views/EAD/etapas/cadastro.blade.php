<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$IDCurso)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
           <form action="{{route('EAD/Etapas/Save')}}" method="POST">
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
            @if(isset($Registro))
            <input type="hidden" name="id" value="{{$Registro->id}}">
            @endif
            <input type="hidden" name="IDCurso" value="{{$IDCurso}}">
            <div class="col-sm-12">
                <label>Nome da Etapa</label>
                <input type="text" name="NMEtapa" class="form-control" value="{{isset($Registro) ? $Registro->NMEtapa : ''}}">
            </div>
            <div class="col-sm-12">
                <label>Descrição da Etapa</label>
                <textarea name="DSEtapa" class="form-control">{{isset($Registro) ? $Registro->DSEtapa : ''}}</textarea>
            </div>
            <br>
            <div class="col-auto">
                <button class="btn btn-fr">Salvar</button>
                <a href="{{route('EAD/Etapas',$IDCurso)}}" class="btn btn-light">Voltar</a>
            </div>
           </form>
        </div>
    </div>
</x-educacional-layout>