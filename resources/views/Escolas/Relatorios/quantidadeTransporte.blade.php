<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Escolas/Relatorios/Gerar',$Tipo)}}" method="POST">
                @csrf
                @method('PUT')
                <button class="btn btn-primary col-auto">Gerar</button>
                <a href="{{route('Escolas/Relatorios')}}" class="btn btn-light col-auto">Voltar</a>
            </form>
        </div>
    </div>
</x-educacional-layout>