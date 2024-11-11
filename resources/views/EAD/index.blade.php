<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('EAD/Save')}}" method="POST">
                @csrf
                <div class="col-sm-12">
                    <label>Orientação aos Pais</label>
                    <textarea name="Orientacao" class="form-control"></textarea>
                </div>
                <div class="col-sm-12">
                    <label>Normativas e Regulamentos</label>
                    <textarea name="Normativas" class="form-control"></textarea>
                </div>
                <br>
                <div class="col-sm-4">
                    <button class="btn btn-fr">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</x-educacional-layout>